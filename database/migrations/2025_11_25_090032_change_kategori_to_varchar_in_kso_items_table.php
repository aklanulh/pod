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
        Schema::table('kso_items', function (Blueprint $table) {
            // Change ENUM to VARCHAR to allow manual input
            $table->string('kategori')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kso_items', function (Blueprint $table) {
            // Revert back to ENUM if needed
            $table->enum('kategori', [
                'hematologi', 
                'kimia_klinik', 
                'gas_darah', 
                'koagulasi', 
                'mikrobiologi', 
                'preparasi_sampel',
                'imaging',
                'monitoring',
                'lainnya'
            ])->nullable()->change();
        });
    }
};
