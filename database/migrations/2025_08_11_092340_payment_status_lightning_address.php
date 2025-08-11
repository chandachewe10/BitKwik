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
        Schema::table('bit_coin_to_mobile_money', function (Blueprint $table) {
            $table->string('payment_status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('lightning_invoice_address')->nullable()->after('qr_code_path');
            $table->decimal('amount_sats', 16, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bit_coin_to_bank_mobile_money', function (Blueprint $table) {
            //
        });
    }
};
