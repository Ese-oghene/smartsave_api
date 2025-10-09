<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Api\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return new UserResource($request->user()->load('account'));
//     return $request->user();
// });

// Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'me']);


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ----------------- TRANSACTIONS -----------------
    Route::get('/transactions', [TransactionController::class, 'index']); // user’s contributions
    Route::post('/transactions', [TransactionController::class, 'store']); // contribute

    // Admin routes
    Route::get('/admin/transactions', [TransactionController::class, 'all']);
    Route::post('/admin/transactions/approve', [TransactionController::class, 'approve']);
    Route::post('/admin/transactions/approve-user/{id}', [TransactionController::class, 'approveUser']);
    Route::post('/admin/transactions/reject', [TransactionController::class, 'reject']);
    Route::delete('/admin/transactions/{id}', [TransactionController::class, 'destroy']);

    // Admin: view specific user + their accounts + transactions
    Route::get('/admin/users/{id}/transactions', [TransactionController::class, 'userDetails']);


});


