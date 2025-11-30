<?php

namespace App\Http\Controllers\Api;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationApiController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/applications",
     *      operationId="indexApplications",
     *      tags={"Application"},
     *      summary="List aplikasi (admin lihat semua, user lihat milik sendiri)",
     *      security={{"sanctum":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="List applications",
     *          @OA\JsonContent(type="array", @OA\Items(type="object"))
     *      )
     * )
     */
    public function index(Request $req)
    {
        if ($req->user()->role === 'admin') {
            $applications = Application::with('user', 'job')->get();
        } else {
            $applications = Application::where('user_id', $req->user()->id)
                ->with('user', 'job')->get();
        }

        return response()->json($applications);
    }

    /**
     * @OA\Post(
     *      path="/api/jobs/{job}/apply",
     *      operationId="storeApplication",
     *      tags={"Application"},
     *      summary="Submit aplikasi untuk lowongan",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="job", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"cv"},
     *              @OA\Property(property="cv", type="string", format="binary", description="File CV (PDF/DOC/DOCX)")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Aplikasi dikirim"),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $req, Job $job)
    {
        $req->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048'
        ]);

        $cvPath = $req->file('cv')->store('cvs', 'public');

        $app = Application::create([
            'user_id' => $req->user()->id,
            'job_id' => $job->id,
            'cv' => $cvPath,
            'status' => 'Pending'
        ]);

        return response()->json(['message' => 'Application submitted', 'application' => $app], 201);
    }

    /**
     * @OA\Patch(
     *      path="/api/applications/{application}/status",
     *      operationId="updateApplicationStatus",
     *      tags={"Application"},
     *      summary="Update status aplikasi (admin only - Accepted/Rejected)",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="application", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"status"},
     *              @OA\Property(property="status", type="string", enum={"Accepted","Rejected"}, example="Accepted")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Status updated"),
     *      @OA\Response(response=403, description="Admin access required")
     * )
     */
    public function updateStatus(Request $req, Application $application)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $req->validate([
            'status' => 'required|in:Accepted,Rejected'
        ]);

        $application->update($data);
        return response()->json(['message' => 'Status updated', 'application' => $application]);
    }
}

