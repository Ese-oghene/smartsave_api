<?php

namespace App\Services\Transaction;

use LaravelEasyRepository\BaseService;

interface TransactionService extends BaseService{

    public function contribute($user, array $data);
    // public function approve($id);

    public function userTransactions($user, int $perPage = 5);

    public function allTransactions();

    public function approveUserTransactions($userId);

    public function getUserWithTransactions($userId); // 👈 add this

    public function userSummary($user); // 👈 add this

      public function usersWithPendingStatus(int $perPage = 5);

      public function userPendingTransactions($userId);

      public function getAllContributors(int $perPage = 5);


}
