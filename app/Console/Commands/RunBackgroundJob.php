<?php
namespace App\Console\Commands;

use App\Services\AllowedClassMethodValidator;
use Illuminate\Console\Command;
use App\Services\BackgroundJobRunner;
use App\Services\ClassMethodParamValidator;

class RunBackgroundJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:run {class} {method} {parameters?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a background job via the terminal.';

    /**
     * The background job runner instance.
     *
     * @var \App\Services\BackgroundJobRunner
     */
    protected $backgroundJobRunner;
    protected AllowedClassMethodValidator $classValidator;
    protected ClassMethodParamValidator $paramValidator;
    /**
     * Create a new command instance.
     *
     * @param \App\Services\BackgroundJobRunner $backgroundJobRunner
     */
    public function __construct(BackgroundJobRunner $backgroundJobRunner , AllowedClassMethodValidator $classValidator,
    ClassMethodParamValidator $paramValidator)
    {
        parent::__construct();
        $this->backgroundJobRunner = $backgroundJobRunner;
       
        $this->classValidator = $classValidator;
        $this->paramValidator = $paramValidator;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Get input arguments
        $class = $this->argument('class');
        $method = $this->argument('method');
        $parameters = $this->argument('parameters');

        // If parameters are passed as strings, convert them to actual PHP values (optional)
        if ($parameters) {
            $parameters = array_map(function ($param) {
                return json_decode($param, true) ?? $param;
            }, $parameters);
        }

        try {
            $this->classValidator->validate($class, $method);
            $this->paramValidator->validate($class, $method, $parameters);

            // Run the job
            $this->backgroundJobRunner->runJob($class, $method, $parameters);

            // Inform the user that the job was run successfully
            $this->info("Job {$class}::{$method} executed successfully.");
            $this->info('Please check the job execution logs for more details.');
        } catch (\Exception $e) {
            // If any exception occurs, show an error message
            $this->error("Failed to execute job: " . $e->getMessage());
            $this->error('Please check the job execution logs for more details.');
        }
    }
}
