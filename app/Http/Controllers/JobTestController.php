<?php

namespace App\Http\Controllers;

class JobTestController extends Controller
{
    public function testJobs()
    {
        $results = [];

        // Test allowed class
        try {
            runBackgroundJob('App\\Jobs\\SendEmailJob', 'execute', ['example@example.com', 'hello']);
            $results[] = [
                'type' => 'success',
                'message' => 'Job App\Jobs\SendEmailJob::execute executed successfully.'
            ];
        } catch (\Exception $e) {
            $results[] = [
                'type' => 'error',
                'message' => 'Failed to execute job: ' . $e->getMessage()
            ];
        }

        // Test disallowed class
        try {
            runBackgroundJob('App\\Jobs\\NonExistentJob', 'execute', []);
        } catch (\Exception $e) {
            $results[] = [
                'type' => 'error',
                'message' => 'Disallowed class error: ' . $e->getMessage()
            ];
        }

        // Return the view with the results
        return view('job-test-results', compact('results'));
    }
}
