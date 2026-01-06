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
        Schema::dropIfExists('stock_movements');

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->string('order_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'opname']);
            $table->integer('quantity');
            $table->integer('stock_before')->nullable();
            $table->integer('stock_after')->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->boolean('include_tax')->default(false);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('subtotal_amount', 15, 2)->nullable();
            $table->decimal('final_amount', 15, 2)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_name')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('notes')->nullable();
            $table->integer('payment_terms')->default(30);
            $table->date('transaction_date');
            $table->timestamps();

            // Indexes
            $table->index(['reference_number']);
            $table->index(['product_id']);
            $table->index(['type']);
            $table->index(['transaction_date']);
            $table->index(['supplier_id']);
            $table->index(['customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
