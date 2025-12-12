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
        Schema::table('zesco_bills', function (Blueprint $table) {
            $table->decimal('amount_sats', 16, 8)->nullable();
            $table->decimal('convenience_fee', 16, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zesco_bills', function (Blueprint $table) {
            //
        });
    }
};
