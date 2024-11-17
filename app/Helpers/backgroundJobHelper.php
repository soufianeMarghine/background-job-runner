<?php

use App\Services\BackgroundJobRunner;


/**
 * Global function to run background jobs.
 *
 * @param string $className
 * @param string $methodName
 * @param array $parameters
 * @return void
 */
if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob($className, $methodName, $parameters = [])
    {
        $jobRunner = app(BackgroundJobRunner::class);
        $jobRunner->runJob($className, $methodName, $parameters);
    }
}
