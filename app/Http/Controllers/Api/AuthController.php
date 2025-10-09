<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\loginRequest;
use App\Http\Requests\Auth\registerRequest;
use Illuminate\Http\JsonResponse;
use App\Services\Auth\AuthService;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

      public AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**

     */

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request)->toJson();
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->authService->login($request)->toJson();
    }

    public function logout(Request $request): JsonResponse
    {
        return $this->authService->logout($request)->toJson();
    }

//     public function me(Request $request)
// {
//     $user = $request->user()->load('accounts');

//     return response()->json([
//         'code' => 200,
//         'message' => 'Authenticated user',
//         'data' => [
//             'user' => new UserResource($user),
//         ],
//     ]);
// }


public function me(Request $request)
{
    try {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        // optionally include account info if you have that relation
        $user->load('accounts'); // only if you have an account table

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);

    } catch (\Throwable $e) {
        Log::error('Error fetching user: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Server error while fetching user'
        ], 500);
    }
}


}
