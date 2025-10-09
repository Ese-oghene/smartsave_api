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
        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('account_type', ['savings', 'shares'])->default('savings')->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
           $table->dropColumn('account_type');
        });
    }
};


// C:\Users\USER\3D Objects\SmartSave\database\migrations\2025_09_30_192955_add_account_type_to_accounts_table.php


