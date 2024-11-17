<?php

namespace App\Http\Controllers;

class JobTestController extends Controller
{
    public function testJobs()
    {
         // Test allowed class
         runBackgroundJob('App\\Jobs\\SendEmailJob', 'execute', ['example@example.com']);

         // Test disallowed class (this should fail)
         try {
             runBackgroundJob('App\\Jobs\\NonExistentJob', 'execute', []);
         } catch (\Exception $e) {
             echo  $e->getMessage(); // "The class 'App\\Jobs\\NonExistentJob' is not allowed to run as a background job."
         }

    
    }
}
