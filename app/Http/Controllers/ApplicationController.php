<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request, $job_id)
    {
        $request->validate([
            'cv' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);

        $cvPath = $request->file('cv')->store('cvs', 'public');

        \App\Models\Application::create([
            'user_id' => auth()->id(),
            'job_id' => $job_id,
            'cv' => $cvPath,
        ]);

        return redirect()->back()->with('success', 'Application submitted successfully.');
    }

    public function approve($id)
    {
        $application = \App\Models\Application::findOrFail($id);
        $application->update(['status' => 'approved']);
        return redirect()->back()->with('success', 'Application approved successfully.');
    }

    public function reject($id)
    {
        $application = \App\Models\Application::findOrFail($id);
        $application->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Application rejected successfully.');
    }

    public function export(Request $request)
    {
        $applications = \App\Models\Application::with(['user', 'jobVacancy.job']);
        if ($request->job_id) {
            $applications = $applications->where('job_id', $request->job_id);
        }
        $applications = $applications->get();

        $filename = 'applications_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($applications) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Applicant', 'Job Title', 'Position', 'Status', 'Applied At']);
            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->user->name,
                    $app->jobVacancy->job->title,
                    $app->jobVacancy->position,
                    $app->status,
                    $app->created_at->format('Y-m-d'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
