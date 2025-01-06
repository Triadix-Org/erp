<?php

use App\Http\Controllers\JobVacancyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/root');
});

Route::get('/were-hiring', JobVacancyController::class)->name('job-vacancy');
Route::get('/job/{slug}', [JobVacancyController::class, 'job'])->name('job-details');
Route::get('/job/{slug}/apply', [JobVacancyController::class, 'apply'])->name('job-apply');

include __DIR__ . '/pdf.php';
