<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobTestController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/run-job', [JobTestController::class, 'testJobs']);