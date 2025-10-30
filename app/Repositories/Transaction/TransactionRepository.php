<?php

namespace App\Repositories\Transaction;

use LaravelEasyRepository\Repository;

interface TransactionRepository extends Repository{

    public function createContribution($data);
    public function findById($id);
    public function allWithUsers();
    public function userTransactions(array $accountId);

    // 👇 Add this so Intelephense & PHP know it exists
    public function query();

      // 👇 New method
    public function getUsersWithPendingContributions(int $perPage = 5);
    public function getUserPendingTransactions($userId);

    public function getAllContributors($perPage = 5);
}
