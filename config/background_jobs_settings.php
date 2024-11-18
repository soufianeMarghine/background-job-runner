<?php

return [
    // Define allowed classes that can be executed as background jobs.
    'allowed_classes' => [
        \App\Jobs\SendEmailJob::class => [
            'execute', // List the allowed methods for this class
        ],
    ],

    // Maximum number of retry attempts for failed jobs
    'max_retries' => 3,

    // Log file for background jobs
    'log_file' => storage_path('logs/background_jobs.log'),

    // Error log file for failed jobs
    'error_log_file' => storage_path('logs/background_jobs_errors.log'),

    // Default job priority
    'default_priority' => 1,

    // Job delay (in seconds) before executing a job
    'default_delay' => 0,
];
