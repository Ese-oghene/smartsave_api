<?php

namespace App\Repositories\Transaction;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Transaction;

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

    public function userTransactions($accountId)
    {
        return $this->model->whereIn('account_id', $accountId)
        ->with('account') // 👈 include the related account
        ->latest()
        ->get();
    }


    public function query()
    {
        return $this->model->newQuery();
    }


}
