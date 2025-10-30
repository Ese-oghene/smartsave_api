<?php

namespace App\Services\Transaction;

use App\Http\Resources\userResource;
use LaravelEasyRepository\ServiceApi;
use App\Repositories\Transaction\TransactionRepository;
use App\Http\Resources\TransactionResource;
use App\Models\User;



class TransactionServiceImplement extends ServiceApi implements TransactionService{

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
     protected TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
      $this->transactionRepository = $transactionRepository;
    }

    /**
     * User contributes — system splits into savings + shares
     */
    public function contribute($user, array $data): TransactionServiceImplement
    {
        try {
            $amount = $data['amount'];

            // Split logic — here 50/50 (can be changed later)
            $savingsPart = $amount / 2;
            $sharesPart  = $amount / 2;

            // Find user accounts
            $savingsAccount = $user->accounts()->where('account_type', 'savings')->first();
            $sharesAccount  = $user->accounts()->where('account_type', 'shares')->first();

            if (!$savingsAccount || !$sharesAccount) {
                return $this->setCode(400)
                    ->setMessage("User must have both Savings and Shares accounts before contributing");
            }

            // Create two pending transactions (not approved yet)
            $transactions = [];

            $transactions[] = $this->transactionRepository->createContribution([
                'account_id'  => $savingsAccount->id,
                'type'        => 'contribution',
                'amount'      => $savingsPart,
                'description' => 'Contribution to Savings',
                'status'      => 'pending',
                'receipt_path' => $data['receipt_path'] ?? null, // ✅ added
            ]);

            $transactions[] = $this->transactionRepository->createContribution([
                'account_id'  => $sharesAccount->id,
                'type'        => 'contribution',
                'amount'      => $sharesPart,
                'description' => 'Contribution to Shares',
                'status'      => 'pending',
                'receipt_path' => $data['receipt_path'] ?? null, // ✅ added
            ]);

            return $this->setCode(201)
                ->setMessage("Contribution submitted and pending approval (split into Savings & Shares)")
                ->setData(TransactionResource::collection($transactions));

        } catch (\Exception $e) {
            return $this->setCode(400)
                ->setMessage("Contribution Failed")
                ->setError($e->getMessage());
        }
    }

    public function approveUserTransactions($userId): TransactionServiceImplement
    {
        try {
            $user = User::with('accounts')->findOrFail($userId);

            $pendingTransactions = $this->transactionRepository->query()
                ->whereHas('account', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->where('status', 'pending')
                ->get();

            if ($pendingTransactions->isEmpty()) {
                return $this->setCode(400)
                    ->setMessage("No pending contributions for this user");
            }

            $transactionsData = [];

            foreach ($pendingTransactions as $transaction) {
                $transaction->update(['status' => 'approved']);

                $account = $transaction->account;
                $account->balance += $transaction->amount;
                $account->save();

                $transactionsData[] = new TransactionResource($transaction);
            }

            return $this->setCode(200)
                ->setMessage("All pending contributions approved for user")
                ->setData([
                    'transactions' => $transactionsData,
                    'accounts'     => $user->accounts->map(fn($acc) => [
                        'type'           => $acc->account_type,
                        'account_number' => $acc->account_number,
                        'balance'        => $acc->balance,
                    ]),
                    'total_balance' => $user->accounts->sum('balance'),
                ]);

        } catch (\Exception $e) {
            return $this->setCode(400)
                ->setMessage("Approval Failed")
                ->setError($e->getMessage());
        }
    }


    public function userTransactions($user, $perPage = 5): TransactionServiceImplement
    {
        try {
            $transactions = $this->transactionRepository->userTransactions($user->accounts->pluck('id')->toArray())
            ->paginate($perPage); // ✅ now uses dynamic per-page value

            return $this->setCode(200)
                ->setMessage("User transactions retrieved successfully")
                ->setData([
                'data' => TransactionResource::collection($transactions)->items(),
                'meta' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
            ]);
                //  ->setData(TransactionResource::collection($transactions));
        } catch (\Exception $e) {
            return $this->setCode(400)->setMessage("Fetch Failed")->setError($e->getMessage());
        }
    }


    public function allTransactions(): TransactionServiceImplement
    {
        try {
            $transactions = $this->transactionRepository->allWithUsers();

            return $this->setCode(200)
                ->setMessage("All contributions retrieved successfully")
               ->setData(TransactionResource::collection($transactions));
        } catch (\Exception $e) {
            return $this->setCode(400)
                ->setMessage("Fetch Failed")
                ->setError($e->getMessage());
        }
    }

    public function delete($id): TransactionServiceImplement
    {
        try {
            $transaction = $this->transactionRepository->findById($id);

            if (!$transaction) {
                return $this->setCode(404)
                    ->setMessage("Transaction not found");
            }

            // Only allow delete if it's not approved (safety rule, optional)
            if ($transaction->status === 'approved') {
                return $this->setCode(400)
                    ->setMessage("Approved transactions cannot be deleted. Reject instead.");
            }

            $transaction->delete();

            return $this->setCode(200)
                ->setMessage("Transaction deleted successfully");
        } catch (\Exception $e) {
            return $this->setCode(400)
                ->setMessage("Delete Failed")
                ->setError($e->getMessage());
        }
    }

    public function getUserWithTransactions($userId): TransactionServiceImplement
{
    try {
        $user = User::with(['accounts.transactions' => fn($q) => $q->latest()])
            ->findOrFail($userId);

        return $this->setCode(200)
            ->setMessage("User details with contributions retrieved successfully")
            ->setData(new userResource($user));

    } catch (\Exception $e) {
        return $this->setCode(400)
            ->setMessage("Failed to fetch user details")
            ->setError($e->getMessage());
    }
}


public function userSummary($user): TransactionServiceImplement
{
    try {
        $transactions = $this->transactionRepository
            ->userTransactions($user->accounts->pluck('id')->toArray())
            ->get(); // ✅ Get all, no pagination

        $totalContributions = $transactions
            ->where('type', 'contribution')
            ->where('status', 'approved')
            ->sum('amount');

        $pendingContributions = $transactions
            ->where('type', 'contribution')
            ->where('status', 'pending')
            ->sum('amount');

        $totalWithdrawals = $transactions
            ->where('type', 'withdrawal')
            ->where('status', 'approved')
            ->sum('amount');

        $pendingWithdrawals = $transactions
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->sum('amount');

        $totalBalance = $totalContributions - $totalWithdrawals;

        return $this->setCode(200)
            ->setMessage("User summary retrieved successfully")
            ->setData([
                'total_balance' => $totalBalance,
                'total_contributions' => $totalContributions,
                'pending_contributions' => $pendingContributions,
                'total_withdrawals' => $totalWithdrawals,
                'pending_withdrawals' => $pendingWithdrawals,
            ]);
    } catch (\Exception $e) {
        return $this->setCode(400)
            ->setMessage("Failed to fetch summary")
            ->setError($e->getMessage());
    }
}


// New method to get users with pending contributions
    public function usersWithPendingStatus(int $perPage = 2): TransactionServiceImplement
{
    try {
        $users = $this->transactionRepository->getUsersWithPendingContributions($perPage);

        return $this->setCode(200)
            ->setMessage('Users with pending contributions retrieved successfully')
            ->setData($users);
    } catch (\Exception $e) {
        return $this->setCode(400)
            ->setMessage('Failed to fetch users with pending transactions')
            ->setError($e->getMessage());
    }
}


public function getAllContributors(int $perPage = 10): TransactionServiceImplement
{
    try {
        // Call repository
        $contributors = $this->transactionRepository->getAllContributors($perPage);

        return $this->setCode(200)
            ->setMessage('Contributors retrieved successfully')
            ->setData($contributors);

    } catch (\Exception $e) {
        return $this->setCode(400)
            ->setMessage('Failed to fetch contributors')
            ->setError($e->getMessage());
    }
}


    public function userPendingTransactions($userId)
{
    try {
        $pendingTransactions = $this->transactionRepository->getUserPendingTransactions($userId);

        return $this->setCode(200)
            ->setMessage("Pending contributions retrieved successfully")
            ->setData(TransactionResource::collection($pendingTransactions)); // ✅ use resource here
            // ->setData($pendingTransactions);
    } catch (\Exception $e) {
        return $this->setCode(400)
            ->setMessage("Failed to fetch user's pending transactions")
            ->setError($e->getMessage());
    }
}




}
