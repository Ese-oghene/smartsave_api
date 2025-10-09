<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Auth\AuthService;
use App\Services\Auth\AuthServiceImplement;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryImplement;
use App\Services\Transaction\TransactionService;
use App\Services\Transaction\TransactionServiceImplement;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\Transaction\TransactionRepositoryImplement;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->bind(AuthService::class, AuthServiceImplement::class);

         // Add this binding for the repository
        $this->app->bind(
            UserRepository::class,
            UserRepositoryImplement::class
        );

         $this->app->bind(TransactionService::class, TransactionServiceImplement::class);
            // Add this binding for the repository if needed

        $this->app->bind(
            TransactionRepository::class,
            TransactionRepositoryImplement::class
        );


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
