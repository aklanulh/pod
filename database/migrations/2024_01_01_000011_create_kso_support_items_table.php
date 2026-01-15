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
        Schema::create('kso_support_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kso_item_id')->constrained('kso_items')->onDelete('cascade');
            $table->string('nama_item');
            $table->decimal('nilai_item', 15, 2);
            $table->integer('jumlah')->default(1);
            $table->text('spesifikasi')->nullable();

            // Equipment Details
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('no_registrasi')->nullable();
            $table->date('tanggal_install')->nullable();
            $table->string('kategori')->nullable();
            $table->date('garansi_berakhir')->nullable();
            $table->date('periode_kso_mulai')->nullable();
            $table->date('periode_kso_berakhir')->nullable();
            $table->string('lokasi_penempatan')->nullable();
            $table->string('kondisi')->default('excellent');
            $table->string('status')->default('active');

            $table->timestamps();

            $table->index('kso_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kso_support_items');
    }
};
