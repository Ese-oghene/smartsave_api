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
use App\Models\User;
use App\Models\Transaction;
use App\Models\Account;

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

        $data = $request->validated();

        // ✅ Handle file upload
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $data['receipt_path'] = $path;
            $data['receipt_url']  = url("storage/$path"); // ✅ add full URL for frontend
        }

        return $this->transactionService
            ->contribute($request->user(), $data)
            ->toJson();

        // $data = $request->validated();

        // if ($request->hasFile('receipt')) {
        //     $path = $request->file('receipt')->store('receipts', 'public');
        //     $data['receipt_path'] = $path; // ✅ store file path
        // }
        // return $this->transactionService
        //     ->contribute($request->user(), $request->validated())
        //     ->toJson();
    }

     // User sees their contributions
    public function index(Request $request)
{
    $perPage = $request->get('per_page', 5);
    return $this->transactionService->userTransactions($request->user(), $perPage)->toJson();
}

    // Admin: view all
    public function all(): JsonResponse
    {
        return $this->transactionService->allTransactions()->toJson();
    }

     // Admin: approve
    public function approveUser($userId): JsonResponse
    {
        return $this->transactionService
            ->approveUserTransactions($userId)
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

    public function summary(Request $request)
    {
        return $this->transactionService
            ->userSummary($request->user())
            ->toJson();
    }


    public function usersWithPendingStatus(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 5);

        return $this->transactionService
            ->usersWithPendingStatus($perPage)
            ->toJson();
    }

// get all contributor
    public function getAllContributors(Request $request): JsonResponse
{
    $perPage = $request->get('per_page', 5);

    return $this->transactionService
        ->getAllContributors($perPage)
        ->toJson();
}



    // GET /admin/users/{id}/pending-transactions  Returns that specific user’s pending contributions.
    public function userPendingTransactions($id): JsonResponse
    {
        return $this->transactionService
            ->userPendingTransactions($id)
            ->toJson();
    }


    public function getAdminSummary()
    {
        $totalMembers = User::count();

        $totalContributions = Transaction::where('type', 'contribution')
            ->where('status', 'approved')
            ->sum('amount');

        $pendingContributions = Transaction::where('type', 'contribution')
            ->where('status', 'pending')
            ->sum('amount');

        $totalWithdrawals = Transaction::where('type', 'withdrawal')
            ->where('status', 'approved')
            ->sum('amount');

        $pendingWithdrawals = Transaction::where('type', 'withdrawal')
            ->where('status', 'pending')
            ->sum('amount');

        $totalBalance = Account::sum('balance');

        return response()->json([
            'data' => [
                'total_members' => $totalMembers,
                'total_balance' => $totalBalance,
                'total_contributions' => $totalContributions,
                'pending_contributions' => $pendingContributions,
                'total_withdrawals' => $totalWithdrawals,
                'pending_withdrawals' => $pendingWithdrawals,
            ]
        ]);
    }








}
