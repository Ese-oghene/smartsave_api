<?php

namespace App\Services\Transaction;

use LaravelEasyRepository\BaseService;

interface TransactionService extends BaseService{

    public function contribute($user, array $data);
    // public function approve($id);
    public function reject($id);
    public function userTransactions($user);
    public function allTransactions();

    public function approveUserTransactions($userId);

    public function getUserWithTransactions($userId); // 👈 add this
}
