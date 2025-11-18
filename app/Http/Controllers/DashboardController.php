<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [];
        if (auth()->user()->isApplicant()) {
            $data['jobVacancies'] = JobVacancy::with('job')->get();
            $data['myApplications'] = auth()->user()->applications()->with('jobVacancy.job')->get();
            $data['notifications'] = auth()->user()->notifications;
        } elseif (auth()->user()->isAdmin()) {
            $applications = \App\Models\Application::with(['user', 'jobVacancy.job']);
            if (request('job_id')) {
                $applications = $applications->where('job_id', request('job_id'));
            }
            $data['applications'] = $applications->get();
            $data['jobs'] = \App\Models\Job::all();
            $data['notifications'] = auth()->user()->notifications;
        }
        return view('dashboard', $data);
    }
}
