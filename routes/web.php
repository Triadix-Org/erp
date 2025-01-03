<?php

use App\Http\Controllers\JobVacancyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/were-hiring', JobVacancyController::class)->name('job-vacancy');

include __DIR__ . '/pdf.php';
