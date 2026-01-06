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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('minimum_stock');
            $table->foreignId('migrated_to_product_id')->nullable()->after('is_active');
            $table->text('migration_notes')->nullable()->after('migrated_to_product_id');

            // Indexes
            $table->index(['is_active']);
            $table->index(['migrated_to_product_id']);

            // Foreign key constraint
            $table->foreign('migrated_to_product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['migrated_to_product_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['migrated_to_product_id']);
            $table->dropColumn(['is_active', 'migrated_to_product_id', 'migration_notes']);
        });
    }
};
