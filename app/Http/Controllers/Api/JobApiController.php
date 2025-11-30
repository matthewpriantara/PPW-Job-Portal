<?php

namespace App\Http\Controllers\Api;

use App\Models\Job;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class JobApiController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/jobs",
     *     summary="Get all job listings",
     *     tags={"Jobs"},
     *     security={{"bearerAuth":{}}},
        *     @OA\Parameter(
        *         name="company",
        *         in="query",
        *         description="Filter by company name",
        *         required=false,
        *         @OA\Schema(type="string")
        *     ),
        *     @OA\Parameter(
        *         name="location",
        *         in="query",
        *         description="Filter by location",
        *         required=false,
        *         @OA\Schema(type="string")
        *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of jobs",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="company", type="string"),
     *                 @OA\Property(property="location", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $req)
    {
        $q = Job::query();

        // Search Feature
        if ($req->filled('keyword')) {
            $kw = $req->keyword;
            $q->where(function($s) use ($kw) {
                $s->where('title', 'like', "%$kw%")
                  ->orWhere('description', 'like', "%$kw%");
            });
        }

        // Filter by company (partial match)
        if ($req->filled('company')) {
            $q->where('company', 'like', "%{$req->company}%");
        }

        // Filter by location (partial match)
        if ($req->filled('location')) {
            $q->where('location', 'like', "%{$req->location}%");
        }

        // Pagination
        $jobs = $q->orderBy('created_at', 'desc')->paginate($req->get('per_page', 10));
        return response()->json($jobs);
    }

    /**
     * @OA\Get(
     *      path="/api/public/jobs",
     *      operationId="publicIndex",
     *      tags={"Jobs"},
     *      summary="List lowongan publik (tanpa token)",
     *      @OA\Response(
     *          response=200,
     *          description="List jobs publik",
     *          @OA\JsonContent(type="array", @OA\Items(type="object"))
     *      )
     * )
     */
    public function publicIndex()
    {
        $jobs = Job::orderBy('created_at', 'desc')->limit(20)->get();
        return response()->json($jobs);
    }

    /**
     * @OA\Get(
     *      path="/api/jobs/{job}",
     *      operationId="showJob",
     *      tags={"Jobs"},
     *      summary="Detail lowongan kerja",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="job",
     *          in="path",
     *          description="Job ID",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(response=200, description="Detail job"),
     *      @OA\Response(response=404, description="Job tidak ditemukan")
     * )
     */
    public function show(Job $job)
    {
        return response()->json($job);
    }

    /**
     * @OA\Post(
     *      path="/api/jobs",
     *      operationId="storeJob",
     *      tags={"Jobs"},
     *      summary="Buat lowongan baru (admin only)",
     *      security={{"sanctum":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"title","description"},
     *              @OA\Property(property="title", type="string", example="Software Engineer"),
     *              @OA\Property(property="description", type="string", example="Develop software applications"),
     *              @OA\Property(property="department", type="string", example="IT")
     *          )
     *      ),
     *      @OA\Response(response=201, description="Job dibuat berhasil"),
     *      @OA\Response(response=403, description="Admin access required")
     * )
     */
    public function store(Request $req)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $req->validate([
            'title' => 'required',
            'description' => 'required',
            'department' => 'nullable|string',
        ]);

        $job = Job::create($data);
        return response()->json(['message' => 'Created', 'job' => $job], 201);
    }

    /**
     * @OA\Put(
     *      path="/api/jobs/{job}",
     *      operationId="updateJob",
     *      tags={"Jobs"},
     *      summary="Update lowongan (admin only)",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="job", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="title", type="string"),
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="department", type="string")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Job updated"),
     *      @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function update(Request $req, Job $job)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $req->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'department' => 'sometimes|string',
        ]);

        $job->update($data);
        return response()->json(['message' => 'Updated', 'job' => $job]);
    }

    /**
     * @OA\Delete(
     *      path="/api/jobs/{job}",
     *      operationId="destroyJob",
     *      tags={"Jobs"},
     *      summary="Hapus lowongan (admin only)",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(name="job", in="path", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Job deleted"),
     *      @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function destroy(Request $req, Job $job)
    {
        if ($req->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $job->delete();
        return response()->json(['message' => 'Deleted']);
    }
}

