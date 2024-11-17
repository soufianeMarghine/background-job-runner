<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class BackgroundJobRunner
{
    protected AllowedClassMethodValidator $classValidator;
    protected ClassMethodParamValidator $paramValidator;
    protected JobExecutionLogger $jobExecutionLogger;

    public function __construct(
        AllowedClassMethodValidator $classValidator,
        ClassMethodParamValidator $paramValidator,
        JobExecutionLogger $jobExecutionLogger
    ) {
        $this->classValidator = $classValidator;
        $this->paramValidator = $paramValidator;
        $this->jobExecutionLogger = $jobExecutionLogger;
    }

    /**
     * Run the given class and method with parameters.
     *
     * @param string $className
     * @param string $methodName
     * @param array $parameters
     * @return void
     * @throws Exception
     */
    public function run(string $className, string $methodName, array $parameters = [])
    {
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
        } catch (Exception $e) {
            // Log failure
            $this->jobExecutionLogger->logFailure($className, $methodName, $e->getMessage());

            // Re-throw the exception
            throw $e;
        }
    }
}
