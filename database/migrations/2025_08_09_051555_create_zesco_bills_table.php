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
        Schema::create('zesco_bills', function (Blueprint $table) {
            $table->id();
             $table->string('meter_number');
            $table->decimal('amount_btc', 16, 8)->nullable();
             $table->decimal('amount_kwacha', 16, 8);
            $table->string('package')->nullable();

            // Delivery step fields
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('delivery_email')->nullable();

            // Billing step fields
            $table->string('queue')->index(); // For processing queue
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('transaction_id')->nullable(); // BTC transaction ID
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zesco_bills');
    }
};
