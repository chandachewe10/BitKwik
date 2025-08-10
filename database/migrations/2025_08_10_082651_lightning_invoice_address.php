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
            $table->text('lightning_invoice_address')->nullable()->after('qr_code_path');
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
