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
            // Add unique_id column (6-8 digit numeric string)
            $table->string('unique_id', 8)->unique()->nullable()->after('id');
            // Add index for faster lookup
            $table->index('unique_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kso_items', function (Blueprint $table) {
            $table->dropIndex(['unique_id']);
            $table->dropColumn('unique_id');
        });
    }
};
