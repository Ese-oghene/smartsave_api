<?php

namespace App\Repositories\Transaction;

use LaravelEasyRepository\Repository;

interface TransactionRepository extends Repository{

    public function createContribution($data);
    public function findById($id);
    public function allWithUsers();
    public function userTransactions($accountId);


    // 👇 Add this so Intelephense & PHP know it exists
    public function query();
}
