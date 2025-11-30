<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *      title="Job Portal API",
 *      version="1.0.0",
 *      description="Swagger documentation for the Job Portal service"
 * )
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="Server local"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/register",
     *      operationId="register",
     *      tags={"Authentication"},
     *      summary="Register user baru",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="password", type="string", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User berhasil didaftarkan",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Registered"),
     *              @OA\Property(property="user", type="object"),
     *              @OA\Property(property="token", type="string")
     *          )
     *      ),
     *      @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $req)
    {
        $data = $req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'applicant',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['message' => 'Registered', 'user' => $user, 'token' => $token], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Authentication"},
     *      summary="Login user",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", example="john@example.com"),
     *              @OA\Property(property="password", type="string", example="password123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login berhasil",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string"),
     *              @OA\Property(property="token", type="string"),
     *              @OA\Property(property="user", type="object")
     *          )
     *      ),
     *      @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $req)
    {
        $credentials = $req->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      operationId="logout",
     *      tags={"Authentication"},
     *      summary="Logout user",
     *      security={{"sanctum":{}}},
     *      @OA\Response(response=200, description="Logout berhasil")
     * )
     */
    public function logout(Request $req)
    {
        $req->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}

