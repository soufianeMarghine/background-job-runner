<?php

namespace App\Http\Controllers;

class JobController extends Controller
{
    public function runJob()
    {
        // Call the helper function to run the job
        runBackgroundJob('App\\Jobs\\SendEmailJob', 'execute', ['example@example.com']);
    }
}
