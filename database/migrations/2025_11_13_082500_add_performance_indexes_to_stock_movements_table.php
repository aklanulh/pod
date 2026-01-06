<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            // Index for customer reports (customer_id + type + transaction_date)
            $table->index(['customer_id', 'type', 'transaction_date'], 'idx_customer_type_date');
            
            // Index for supplier reports (supplier_id + type + transaction_date)
            $table->index(['supplier_id', 'type', 'transaction_date'], 'idx_supplier_type_date');
            
            // Index for product reports (product_id + type + transaction_date)
            $table->index(['product_id', 'type', 'transaction_date'], 'idx_product_type_date');
            
            // Index for chart data queries (customer_id + type + year/month)
            $table->index(['customer_id', 'type'], 'idx_customer_type');
            $table->index(['product_id', 'type'], 'idx_product_type');
            
            // Index for transaction_date queries (year/month filtering)
            $table->index('transaction_date', 'idx_transaction_date');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex('idx_customer_type_date');
            $table->dropIndex('idx_supplier_type_date');
            $table->dropIndex('idx_product_type_date');
            $table->dropIndex('idx_customer_type');
            $table->dropIndex('idx_product_type');
            $table->dropIndex('idx_transaction_date');
        });
    }
};
