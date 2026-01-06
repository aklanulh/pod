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
        Schema::table('stock_out_drafts', function (Blueprint $table) {
            $table->string('bank_option')->default('mandiri_cibubur')->after('delivery_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_out_drafts', function (Blueprint $table) {
            $table->dropColumn('bank_option');
        });
    }
};
