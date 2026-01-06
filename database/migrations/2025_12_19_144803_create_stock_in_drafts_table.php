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
        Schema::create('stock_in_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('draft_number')->unique();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('supplier_name');
            $table->string('order_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->boolean('include_tax')->default(false);
            $table->json('cart_data');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['draft_number']);
            $table->index(['supplier_id']);
            $table->index(['transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_in_drafts');
    }
};
