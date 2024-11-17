<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class BackgroundJobRunner
{
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
            // Validate class existence
            if (!class_exists($className)) {
                throw new Exception("Class $className does not exist.");
            }

            // Instantiate the class
            $classInstance = new $className();

            // Validate method existence
            if (!method_exists($classInstance, $methodName)) {
                throw new Exception("Method $methodName does not exist in class $className.");
            }

            // Call the method with parameters
            call_user_func_array([$classInstance, $methodName], $parameters);

            // Log success
            Log::info("Background job executed successfully.", [
                'class' => $className,
                'method' => $methodName,
                'parameters' => $parameters,
                'status' => 'success',
                'timestamp' => now(),
            ]);
        } catch (Exception $e) {
            // Log failure
            Log::error("Background job execution failed.", [
                'class' => $className,
                'method' => $methodName,
                'parameters' => $parameters,
                'status' => 'failed',
                'error' => $e->getMessage(),
                'timestamp' => now(),
            ]);

            // Re-throw the exception
            throw $e;
        }
    }
}
