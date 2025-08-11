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
        Schema::create('bit_coin_to_mobile_money', function (Blueprint $table) {
            $table->id();
            $table->string('mobile_number');
            $table->decimal('amount_btc', 16, 8)->nullable();
            $table->decimal('amount_kwacha', 16, 2);
            $table->string('qr_code_path')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('delivery_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bit_coin_to_mobile_money');
    }
};
