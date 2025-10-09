<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Transaction\TransactionService;
use App\Http\Requests\ContributeRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\ApproveRejectRequest;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }


    // User makes a contribution
    public function store(ContributeRequest $request): JsonResponse
    {
        return $this->transactionService
            ->contribute($request->user(), $request->validated())
            ->toJson();
    }

     // User sees their contributions
    public function index(): JsonResponse
    {
        return $this->transactionService
            ->userTransactions(request()->user())
            ->toJson();
    }

    // Admin: view all
    public function all(): JsonResponse
    {
        return $this->transactionService->allTransactions()->toJson();
    }

     // Admin: approve
    // public function approve(ApproveRejectRequest $request): JsonResponse
    // {
    //     return $this->transactionService
    //         ->approve($request->validated()['transaction_id'])
    //         ->toJson();
    // }


     // Admin: approve
    public function approveUser($userId): JsonResponse
    {
        return $this->transactionService
            ->approveUserTransactions($userId)
            ->toJson();
    }


     // Admin: reject
    public function reject(ApproveRejectRequest $request): JsonResponse
    {
        return $this->transactionService
            ->reject($request->validated()['transaction_id'])
            ->toJson();
    }

    public function destroy($id): JsonResponse
    {
        return $this->transactionService->delete($id)->toJson();
    }

    // Admin: get full user details with transactions
public function userDetails($userId):JsonResponse
{
    return $this->transactionService
        ->getUserWithTransactions($userId)
        ->toJson();
}

}
