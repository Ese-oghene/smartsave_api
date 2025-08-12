<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\loginRequest;
use App\Http\Requests\Auth\registerRequest;
use Illuminate\Http\JsonResponse;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{

      public AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * User Registration
     *
     * Registers a new user in the system with the provided information.
     *
     * @bodyParam first_name string required The user's first name. Example: John
     * @bodyParam last_name string required The user's last name. Example: Doe
     * @bodyParam email string required The user's email address. Example: john.doe@example.com
     * @bodyParam password string required The user's password (min: 8 characters). Example: SecurePass123
     * @bodyParam password_confirmation string required Password confirmation. Example: SecurePass123
     * @bodyParam phone string required The user's phone number. Example: +2348012345678
     *
     * @responseField user object The registered user's information
     * @responseField token string The authentication token for the registered user
     *
     * @response 200 scenario="Success" {
     *     "code": 200,
     *     "message": "Registration successful",
     *     "data": {
     *         "user": {
     *             "id": "67ecf2f9-1c8c-800a-8a31-458b59d9def7",
     *             "name": "john_doe",
     *             "email": "johndoe@email.com",
     *             "phone_no": null,
     *             "avatar": null,
     *             "status": true,
     *             "email_verified_at": null,
     *             "created_at": "2023-10-01T12:00:00Z",
     *             "updated_at": "2023-10-01T12:00:00Z",
     *         }
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *        "permissions": null
     *     }
     * }
     *
     * @response 422 scenario="Validation Error" {
     *     "message": "The given data was invalid",
     *     "errors": {
     *         "email": ["The email has already been taken"],
     *         "password": ["The password must be at least 8 characters"],
     *         "phone": ["The phone number is invalid"]
     *     }
     * }
     */

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->authService->register($request)->toJson();
    }


}
