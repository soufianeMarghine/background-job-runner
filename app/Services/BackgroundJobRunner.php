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
        int $maxRetries = null
    ) {
        $this->classValidator = $classValidator;
        $this->paramValidator = $paramValidator;
        $this->jobExecutionLogger = $jobExecutionLogger;
        $this->maxRetries = $maxRetries ?? config('background_jobs_settings.max_retries', 3);
    }

    /**
     * Run the given class and method with parameters, including retry functionality.
     *
     * @param string $className
     * @param string $methodName
     * @param array $parameters
     * @return void
     * @throws Exception
     */
    public function runJob(string $className, string $methodName, array $parameters = [])
    {
        $retryCount = 0;

        while ($retryCount <= $this->maxRetries) {

            // Log the job as running
            $this->jobExecutionLogger->logJobExecutionStatus($className, $methodName, 'running', $parameters);
            try {
                // Validate the class and method
                $this->classValidator->validate($className, $methodName);

                // Validate method parameters
                $this->paramValidator->validate($className, $methodName, $parameters);

                // Instantiate the class
                if (!class_exists($className)) {
                    throw new Exception("Class $className does not exist.");
                }

                $classInstance = new $className();

                // Validate the method existence
                if (!method_exists($classInstance, $methodName)) {
                    throw new Exception("Method $methodName does not exist in class $className.");
                }

                // Call the method with parameters
                call_user_func_array([$classInstance, $methodName], $parameters);

                // Log success
                $this->jobExecutionLogger->logSuccess($className, $methodName, $parameters);

                // Exit the loop on success
                break;
            } catch (Exception $e) {
                // Log failure and retry attempt
                $this->jobExecutionLogger->logFailure($className, $methodName, $e->getMessage());
                $this->jobExecutionLogger->logRetry($className, $methodName, $retryCount + 1);

                // Increment retry count
                $retryCount++;

                // If maximum retries are reached, throw the exception
                if ($retryCount > $this->maxRetries) {
                    throw new Exception("Job failed after $retryCount retries: " . $e->getMessage());
                }

                // Delay between retries 
                $defaultDelay = config('background_jobs_settings.default_delay', 0);

                sleep($defaultDelay);  // Add a delay between retries 
            }
        }
    }
}
