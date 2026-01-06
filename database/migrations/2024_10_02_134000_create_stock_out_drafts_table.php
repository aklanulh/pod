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
        Schema::create('stock_out_drafts', function (Blueprint $table) {
            $table->id();
            $table->string('draft_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('customer_name');
            $table->string('order_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->boolean('include_tax')->default(false);
            $table->json('cart_data'); // Store product cart as JSON
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_out_drafts');
    }
};
