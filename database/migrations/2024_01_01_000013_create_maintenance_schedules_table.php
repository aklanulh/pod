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
        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kso_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('kso_support_item_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('equipment_name');
            $table->string('equipment_type'); // 'main' or 'support'
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date');
            $table->string('maintenance_type')->default('preventive'); // preventive, corrective, calibration
            $table->text('description')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, overdue, cancelled
            $table->text('notes')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('technician')->nullable();
            $table->text('technician_notes')->nullable()->after('technician');
            $table->timestamps();

            $table->index(['next_maintenance_date', 'status']);
            $table->index(['kso_item_id', 'equipment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_schedules');
    }
};
