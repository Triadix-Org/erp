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

    public function job($slug)
    {
        $job = JobVacancy::where('slug', $slug)->first();
        return view('guest.job-vacancy.job', compact('job'));
    }

    public function apply($slug)
    {
        $job = JobVacancy::where('slug', $slug)->first();
        return view('guest.job-vacancy.apply', compact('job'));
    }
}
