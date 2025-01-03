<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;

class JobVacancyController extends Controller
{
    public function __invoke()
    {
        $vacancy = JobVacancy::where('status', 1)->get();
        return view('guest.job-vacancy.index', compact('vacancy'));
    }
}
