<?php

namespace App\Services;

use Exception;

class BackgroundJobRunner
{
    protected AllowedClassMethodValidator $classValidator;
    protected ClassMethodParamValidator $paramValidator;
    protected JobExecutionLogger $jobExecutionLogger;
    protected int $maxRetries;

    public function __construct(
        AllowedClassMethodValidator $classValidator,
        ClassMethodParamValidator $paramValidator,
        JobExecutionLogger $jobExecutionLogger,
        int $maxRetries = 3 // Default value directly here
    ) {
        $this->classValidator = $classValidator;
        $this->paramValidator = $paramValidator;
        $this->jobExecutionLogger = $jobExecutionLogger;
        $this->maxRetries = $maxRetries;
    }

    public function runJob(string $className, string $methodName, array $parameters = [])
    {
        $retryCount = 0;

        while ($retryCount < $this->maxRetries) {
            $this->jobExecutionLogger->logJobExecutionStatus($className, $methodName, 'running', $parameters);

            try {
                // Perform class, method, and parameter validation
                $this->classValidator->validate($className, $methodName);
                $this->paramValidator->validate($className, $methodName, $parameters);

                // Create an instance of the class and invoke the method
                $classInstance = new $className();
                call_user_func_array([$classInstance, $methodName], $parameters);

                // Log success and exit loop
                $this->jobExecutionLogger->logSuccess($className, $methodName, $parameters);
                return; // Exit loop upon success
            } catch (Exception $e) {
                $retryCount++;

                // Log failure and retry attempt
                $this->jobExecutionLogger->logFailure($className, $methodName, $e->getMessage());
                $this->jobExecutionLogger->logRetry($className, $methodName, $retryCount);

                if ($retryCount >= $this->maxRetries) {
                    // Log and rethrow the final exception
                    $this->jobExecutionLogger->logFailure(
                        $className,
                        $methodName,
                        "Final failure after $this->maxRetries retries: " . $e->getMessage()
                    );
                    throw new Exception(
                        "Job failed after $this->maxRetries retries. Error: " . $e->getMessage(),
                        $e->getCode(),
                        $e // Preserve the original exception
                    );
                }

                // Retry delay
                $defaultDelay = config('background_jobs_settings.default_delay', 0);
                if ($defaultDelay > 0) {
                    sleep($defaultDelay);
                }
            }
        }
    }
}
