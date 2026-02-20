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
        Schema::create('qc_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kso_item_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['qc', 'calibration']);
            $table->date('date');
            $table->enum('status', ['pass', 'fail', 'pending'])->default('pending');
            $table->string('technician_name')->nullable();
            $table->text('notes')->nullable();
            $table->string('certificate_file')->nullable();
            $table->date('next_due_date')->nullable();
            $table->json('parameters')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['kso_item_id', 'type']);
            $table->index(['type', 'status']);
            $table->index('date');
            $table->index('next_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qc_records');
    }
};
