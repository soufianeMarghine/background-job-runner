<?php

use App\Services\BackgroundJobRunner;
use App\Services\AllowedClassMethodValidator;
use App\Services\ClassMethodParamValidator;
use App\Services\JobExecutionLogger;
use Illuminate\Support\Facades\Log;

/**
 * Global function to run background jobs.
 *
 * @param string $className
 * @param string $methodName
 * @param array $parameters
 * @return void
 */
if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob(string $className, string $methodName, array $parameters = [])
    {
        // Create necessary service instances
        $jobExecutionLogger = new JobExecutionLogger();
        $allowedClassValidator = new AllowedClassMethodValidator();
        $paramValidator = new ClassMethodParamValidator();

        // Create the BackgroundJobRunner instance
        $jobRunner = new BackgroundJobRunner($allowedClassValidator, $paramValidator, $jobExecutionLogger);

        // Run the job using the job runner
        try {
            $jobRunner->run($className, $methodName, $parameters);
        } catch (Exception $e) {
            // Handle any exception that occurs during job execution
            Log::error("Job execution failed: " . $e->getMessage());
        }
    }
}
