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
        Schema::create('kso_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('nama_alat');
            $table->decimal('nilai_alat_utama', 15, 2);
            $table->boolean('butuh_komputer')->default(false);
            $table->decimal('total_pendukung', 15, 2)->default(0);
            $table->decimal('total_investasi', 15, 2);
            $table->text('keterangan')->nullable();
            $table->date('tanggal_investasi');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kso_items');
    }
};
