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
        Schema::create('mobile_to_bitcoins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete()
                ->after('id');
            $table->string('checking_id', 255)->unique()->nullable();
            $table->string('phone_number');
            $table->decimal('amount_sats', 16, 8)->nullable();
            $table->decimal('amount_btc', 16, 8)->nullable();
            $table->decimal('amount_kwacha', 16, 2);
            $table->string('external_id')->nullable();
            $table->string('callback_url')->nullable();
            $table->decimal('total_sats', 16, 8)->nullable();
            $table->decimal('network_fee', 16, 8)->nullable();
            $table->decimal('convenience_fee', 16, 2);
            $table->string('payment_status')->default('pending');
             $table->text('uri')->nullable();
            $table->text('lightning_invoice_address')->nullable();
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_to_bitcoins');
    }
};
