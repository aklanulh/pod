<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_merges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('target_customer_id')->constrained('customers')->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->foreignId('merged_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index(['source_customer_id']);
            $table->index(['target_customer_id']);
            $table->unique(['source_customer_id'], 'unique_source_customer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_merges');
    }
};
