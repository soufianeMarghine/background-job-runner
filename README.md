Here is the final version of your README with the suggested improvements:

---

# Laravel Background Job Runner

This project implements a custom background job runner system for Laravel, allowing you to execute PHP classes and methods in the background outside of Laravel's built-in queue system.

## Features
- Execute PHP classes and methods in the background.
- Support for job retries in case of failure.
- Detailed job execution logs (success, failure, retry attempts).
- Custom Artisan command for triggering jobs via the terminal.
- Configurable retry attempts and job delays.

## Requirements
- PHP 8.0 or higher
- Laravel 11.x or higher

## Installation

### 1. Clone the Repository
Clone this repository to your local machine:

```bash
git clone https://github.com/soufianeMarghine/background-job-runner.git
cd background-job-runner
```

### 2. Install Dependencies
Run the following command to install all dependencies:

```bash
composer install
```

### 3. Configure the Application

#### Configuring Allowed Job Classes

In the `config/background_jobs_settings.php` file, you can define the classes that are allowed to run as background jobs.

Example:

```php
return [
    // Define allowed classes that can be executed as background jobs.
    'allowed_classes' => [
        \App\Jobs\SendEmailJob::class => [
            'execute', // List the allowed methods for this class
        ],
    ],

    // Maximum number of retry attempts for failed jobs
    'max_retries' => 3,

    // Job delay (in seconds) before executing a job
    'default_delay' => 0,
];
```

This configuration ensures that only jobs defined in the `allowed_classes` array can be executed.

### 4. Environment Configuration

You can change the log file location, retry attempts, and delay configuration by modifying the configuration settings in `config/background_jobs_settings.php`.

### 5. Running Jobs via Artisan Command

You can execute background jobs via the terminal using the custom Artisan command:

```bash
php artisan job:run {class} {method} {parameters?*}
```

#### Example:

To run a job that sends an email:

```bash
php artisan job:run App\Jobs\SendEmailJob execute ["email@example.com"]
```

This will run the `SendEmailJob` class's `execute` method and pass the email address as a parameter.

### 6. Job Logs

All job execution results (success, failure, retries) are logged in the `storage/logs/background_jobs_errors.log` file. The log file will contain detailed information about each job's execution status, retry attempts, and errors.

Example log entries:

```json
{
    "class": "App\\Jobs\\SendEmailJob",
    "method": "execute",
    "status": "running",
    "parameters": ["email@example.com", "hello"],
    "timestamp": "2024-11-18T02:38:45.954170Z",
    "retryCount": 0,
    "error": null
}
{
    "class": "App\\Jobs\\SendEmailJob",
    "method": "execute",
    "status": "success",
    "parameters": ["email@example.com", "hello"],
    "timestamp": "2024-11-18T02:38:45.958936Z",
    "retryCount": 0,
    "error": null
}
{
    "class": "App\\Jobs\\NonExistentJob",
    "method": "execute",
    "status": "failed",
    "parameters": [],
    "timestamp": "2024-11-18T02:38:45.961850Z",
    "retryCount": 0,
    "error": "Class 'App\\Jobs\\NonExistentJob' does not exist."
}
```

To view logs, run:

```bash
tail -f storage/logs/background_jobs_errors.log
```

### 7. Retry Mechanism

If a job fails, it will automatically be retried according to the configured retry attempts in `config/background_jobs_settings.php`.

The retry delay is set in the configuration file and applies to all retries. After the maximum number of retries is reached, the job will be marked as failed and logged as such.

### 8. Error Logs

Any errors that occur during job execution are logged in the same `background_jobs_errors.log` file. If the job is retried, the retry count and status are also included in the logs. The error field will contain the error message for failed jobs.

## Example Usage

1. **Send an Email:**
   To run a job that sends an email:

```bash
php artisan job:run App\Jobs\SendEmailJob execute ["email@example.com"]
```

2. **Run a Non-Existent Job:**
   To run a job that doesn't exist (e.g., a job that will fail):

```bash
php artisan job:run App\Jobs\NonExistentJob execute []
```

This will log the error and retry attempts in the `background_jobs_errors.log` file.


## Contributing

To contribute to this project, please ensure you follow the PSR-12 coding standards. Submit your pull requests with a clear description of what changes have been made. If you're fixing a bug or adding a feature, ensure that you add tests to cover the changes.

## License

This project is open-source and available under the MIT License. See the [LICENSE](LICENSE) file for more information.

