<?php

namespace App\Services\Auth;

use LaravelEasyRepository\ServiceApi;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepository;

class AuthServiceImplement extends ServiceApi implements AuthService{

    /**
     * set title message api for CRUD
     * @param string $title
     */
     protected string $title = "";
     /**
     * uncomment this to override the default message
     * protected string $create_message = "";
     * protected string $update_message = "";
     * protected string $delete_message = "";
     */

     /**
     * don't change $this->mainRepository variable name
     * because used in extends service class
     */
     protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
      $this->userRepository = $userRepository;
    }

    // Define your custom methods :)

    public function register($request): AuthServiceImplement
	{
		try {

			$validated = $request->validated();
			$user = $this->userRepository->createUser($validated);

			// Generate Sanctum token
			$token = $user->createToken('API Token')->plainTextToken;
			return $this->setCode(200)
				->setMessage("Registration Successfull")
				->setData([
					'user' => new UserResource($user),
					'token' => $token,
					'permissions' => $user->getAllPermissionNames(),
				]);
		} catch (\Exception $e) {
			return $this->setCode(400)
				->setMessage("Registration Failed")
				->setError($e->getMessage());
		}
	}


    //  public function register($request):AuthServiceImplement
    //  {
    //     try {

	// 		$validated = $request->validated();

	// 		$user = $this->userRepository->createUser($validated);

	// 		// Generate Sanctum token
	// 		$token = $user->createToken('auth_token')->plainTextToken;

	// 		return $this->setCode(200)
	// 			->setMessage("Registration Successfull")
	// 			->setData([
	// 				'user' => new UserResource($user),
	// 				'token' => $token,
	// 			]);



	// 	} catch (\Exception $e) {
	// 		return $this->setCode(400)
	// 			->setMessage("Registration Failed")
	// 			->setError($e->getMessage());
	// 	}
    //  }


     public function login($request): AuthServiceImplement
	{
		try {
			$validated = $request->validated();
			$user = $this->userRepository->findUserByEmail($validated['email']);


			if (!$user || !Hash::check($validated['password'], $user->password)) {
				return $this->setCode(401)->setMessage("Invalid credentials");
			}

			// Revoke old tokens (optional)
			$user->tokens()->delete();

			// Generate Sanctum token
			$token = $user->createToken('auth_token')->plainTextToken;


			return $this->setCode(200)
				->setMessage("Login Success")
				->setData([
					'user' => new UserResource($user),
                    'role' => $user->role, // Assign role properly
					'token' => $token
				]);
		} catch (\Exception $e) {
			return $this->setCode(400)
				->setMessage("Login Failed")
				->setError($e->getMessage());
		}
	}

       public function logout($request): AuthServiceImplement
	{
		try {
			$request->user()->currentAccessToken()->delete();
			return $this->setCode(200)
				->setMessage("Logout Successfull");

		} catch (\Exception $e) {
			return $this->setCode(400)
				->setMessage("Logout Failed")
				->setError($e->getMessage());
		}
	}

       public function adminLogin($request): AuthServiceImplement
	{
		try {
			$validated = $request->validated();
			$user = $this->userRepository->findUserByEmail($validated['email']);

			if (!$user || !Hash::check($validated['password'], $user->password)) {
				return $this->setCode(401)->setMessage("Invalid credentials");
			}

			if (!$user->hasAnyRole(['admin'])) {
				return $this->setCode(403)->setMessage("Forbidden");
			}

			$user->tokens()->delete();


			$token = $user->createToken('auth_token')->plainTextToken;

			return $this->setCode(200)
				->setMessage("Login Success")
				->setData([
                    'user' => new UserResource($user),
					'role' => $user->role,
					'token' => $token
				]);
		} catch (\Exception $e) {
			return $this->setCode(400)
				->setMessage("Admin Login Failed")
				->setError($e->getMessage());
		}
	}

    public function adminLogout($request): AuthServiceImplement
	{
		try {
			$request->user()->currentAccessToken()->delete();
			return $this->setCode(200)
				->setMessage("Logout Successfull");

		} catch (\Exception $e) {
			return $this->setCode(400)
				->setMessage("Logout Failed")
				->setError($e->getMessage());
		}
	}


}
