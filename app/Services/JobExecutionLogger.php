<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class JobExecutionLogger
{
    protected string $logFile;

    public function __construct()
    {
        //log file path
        $this->logFile = storage_path('logs/background_jobs_errors.log');
    }

    /**
     * Log job execution status.
     *
     * @param string $className
     * @param string $methodName
     * @param string $status
     * @param array $parameters
     * @param string|null $error
     * @param int $retryCount
     * @return void
     */
    public function logJobExecutionStatus(
        string $className,
        string $methodName,
        string $status,
        array $parameters = [],
        ?string $error = null,
        int $retryCount = 0
    ): void {
        // Prepare the log data
        $logData = [
            'class' => $className,
            'method' => $methodName,
            'status' => $status,
            'parameters' => $parameters,
            'timestamp' => now(),
            'retryCount' => $retryCount,
            'error' => $error,
        ];

        // Log data as array in JSON format
        $logEntry = json_encode($logData, JSON_PRETTY_PRINT);

        // Write log to file
        file_put_contents($this->logFile, $logEntry . PHP_EOL, FILE_APPEND);
        
        // Alternatively, use Laravel's default logging for a quick debug log
        if ($status === 'failed') {
            Log::error('Job execution failed', $logData);
        } else {
            Log::info('Job execution status', $logData);
        }
    }

    /**
     * Log job retry attempts.
     *
     * @param string $className
     * @param string $methodName
     * @param int $retryCount
     * @return void
     */
    public function logRetry(string $className, string $methodName, int $retryCount): void
    {
        $this->logJobExecutionStatus($className, $methodName, 'retry', [], null, $retryCount);
    }

    /**
     * Log a failed job attempt with error details.
     *
     * @param string $className
     * @param string $methodName
     * @param string $error
     * @return void
     */
    public function logFailure(string $className, string $methodName, string $error): void
    {
        $this->logJobExecutionStatus($className, $methodName, 'failed', [], $error);
    }

    /**
     * Log a successful job execution.
     *
     * @param string $className
     * @param string $methodName
     * @param array $parameters
     * @return void
     */
    public function logSuccess(string $className, string $methodName, array $parameters): void
    {
        $this->logJobExecutionStatus($className, $methodName, 'success', $parameters);
    }
}
