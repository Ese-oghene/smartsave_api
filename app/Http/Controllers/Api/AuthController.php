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


}
