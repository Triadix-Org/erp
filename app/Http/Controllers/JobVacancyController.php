<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Str;

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

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $apply                  = new JobApplication();
            $apply->job_vacancy_id  = $request->job_id;
            $apply->name  = $request->name;
            $apply->phone            = $request->phone;
            $apply->email            = $request->email;
            $apply->date_of_birth            = $request->date_of_birth;
            $apply->education            = $request->education;
            $apply->years_of_experience            = $request->years_of_experience;

            if ($request->hasFile('resume')) {
                $file = $request->file('resume');
                $fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/resume', $fileName);
                $resumePath = 'resume/' . $fileName;
                $apply->resume            = $resumePath;
            }

            if ($request->hasFile('application_letter')) {
                $file = $request->file('application_letter');
                $fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/application_letter', $fileName);
                $application_letterPath = 'application_letter/' . $fileName;
                $apply->application_letter            = $application_letterPath;
            }

            if ($request->hasFile('certificate')) {
                $file = $request->file('certificate');
                $fileName = Str::random(10) . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/certificate', $fileName);
                $certificatePath = 'certificate/' . $fileName;
                $apply->certificate            = $certificatePath;
            }

            $apply->created_at      = now();
            $apply->updated_at      = now();
            $apply->save();

            DB::commit();
            return response()->json([
                'message' => 'success'
            ], 200);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
