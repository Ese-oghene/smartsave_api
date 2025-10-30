<?php

namespace App\Repositories\Transaction;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Transaction;
use App\Models\User;


class TransactionRepositoryImplement extends Eloquent implements TransactionRepository{

    /**
    * Model class to be used in this repository for the common methods inside Eloquent
    * Don't remove or change $this->model variable name
    * @property Model|mixed $model;
    */
    protected Transaction $model;

    public function __construct(Transaction $model)
    {
        $this->model = $model;
    }

    public function createContribution($data)
    {
        return $this->model->create($data);
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function allWithUsers()
    {
        return $this->model->with('account.user')->latest()->get();
    }

    public function userTransactions(array $accountId)
    {
        return $this->model->whereIn('account_id', $accountId)
        ->latest();
    }


    public function query()
    {
        return $this->model->newQuery();
    }

// Returns all users with: Name Total balance Count of pending contributions
    public function getUsersWithPendingContributions($perPage = 5)
{
    $paginator = User::with('accounts.transactions')->paginate($perPage);

    // Extract items (a collection)
    $users = collect($paginator->items());

    // Transform each item
    $data = $users->map(function ($user) {
        $pendingCount = $user->accounts
            ->flatMap->transactions
            ->where('status', 'pending')
            ->where('type', 'contribution')
            ->count();

        $totalBalance = $user->accounts->sum('balance');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'pending_count' => $pendingCount,
            'total_balance' => $totalBalance,
            ];
        });

        return [
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ];
    }



public function getAllContributors($perPage = 5)
{
    $paginator = User::with('accounts.transactions')->paginate($perPage);

    // Extract items (a collection)
    $users = collect($paginator->items());

    // Transform each item
    $data = $users->map(function ($user) {
        $totalContributions = $user->accounts
            ->flatMap->transactions
            ->where('type', 'contribution')
            ->where('status', 'approved')
            ->sum('amount');

        $contributionCount = $user->accounts
            ->flatMap->transactions
            ->where('type', 'contribution')
            ->where('status', 'approved')
            ->count();

        $totalBalance = $user->accounts->sum('balance');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'total_contributions' => $totalContributions,
            'total_contributions_count' => $contributionCount,
            'total_balance' => $totalBalance,
        ];
    });

    // Return paginated response
    return [
        'data' => $data,
        'meta' => [
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
        ],
    ];
}



    public function getUserPendingTransactions($userId)
{
    $user = User::with('accounts.transactions')->findOrFail($userId);

    $pendingTransactions = $user->accounts
        ->flatMap->transactions
        ->where('status', 'pending')
        ->where('type', 'contribution')
        ->values();

    return $pendingTransactions;
}



}
