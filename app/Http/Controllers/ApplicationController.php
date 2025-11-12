<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ApplicationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $filename = 'applications_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return Excel::download(new ApplicationsExport($request->job_id), $filename, \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Securely download an applicant's CV from storage.
     * Accessible to admins or the applicant owner.
     */
    public function downloadCv($id)
    {
        $application = \App\Models\Application::with('user')->findOrFail($id);

        $user = auth()->user();

        // Explicit check: allow admin users or the owner (applicant who uploaded)
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
        $isOwner = $user && $user->id === $application->user_id;

        if (! ($isAdmin || $isOwner)) {
            // Log for auditing
            Log::warning('Unauthorized CV download attempt', [
                'attempt_by_user_id' => $user ? $user->id : null,
                'application_id' => $application->id,
            ]);

            return redirect()->back()->with('error', 'You are not authorized to download this CV.');
        }

        $path = $application->cv;

        if (! $path || ! Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'CV file not found.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'pdf';
        $filename = sprintf('%s_CV.%s', preg_replace('/[^A-Za-z0-9_-]/', '_', $application->user->name ?? 'applicant'), $extension);

        return Storage::disk('public')->download($path, $filename);
    }
}
