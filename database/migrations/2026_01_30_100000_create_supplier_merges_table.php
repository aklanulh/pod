<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_merges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('target_supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->foreignId('merged_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index(['source_supplier_id']);
            $table->index(['target_supplier_id']);
            $table->unique(['source_supplier_id'], 'unique_source_supplier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_merges');
    }
};
