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
        Schema::table('mobile_to_bitcoins', function (Blueprint $table) {
        $table->text('lnurl')->nullable();
        $table->text('qr_code_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobile_to_bitcoins', function (Blueprint $table) {
            //
        });
    }
};
