<?php

namespace App\Services\Auth;

use LaravelEasyRepository\BaseService;

interface AuthService extends BaseService{

    // Write something awesome :)
    public function register($request);
    public function login($request);
    public function logout($request);
    public function adminLogin($request);
}
