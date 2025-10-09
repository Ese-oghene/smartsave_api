<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete(action: 'cascade');
            $table->enum('type', ['deposit', 'withdrawal', 'contribution']);
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
             $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // 👈 added status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};


// C:\Users\USER\3D Objects\SmartSave\database\migrations\2025_08_11_201218_create_transactions_table.php
