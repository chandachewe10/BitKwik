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
        Schema::table('bit_coin_to_bank_accounts', function (Blueprint $table) {
             $table->string('checking_id', 64)->unique()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bit_coin_to_bank_accounts', function (Blueprint $table) {
            //
        });
    }
};
