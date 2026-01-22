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
            $table->string('unique_id', 8)->unique()->nullable();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('nama_alat');

            // Equipment Details
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('no_registrasi')->nullable();

            // Categories (changed from ENUM to VARCHAR)
            $table->string('kategori')->nullable();

            // Installation & Warranty
            $table->date('tanggal_install')->nullable();
            $table->date('garansi_mulai')->nullable();
            $table->date('garansi_berakhir')->nullable();

            // KSO Period
            $table->date('periode_kso_mulai')->nullable();
            $table->date('periode_kso_berakhir')->nullable();
            $table->integer('durasi_kso_bulan')->nullable();

            // Financial
            $table->decimal('nilai_alat_utama', 15, 2);
            $table->boolean('butuh_komputer')->default(false);
            $table->decimal('total_pendukung', 15, 2)->default(0);
            $table->decimal('total_investasi', 15, 2);

            // Additional Details
            $table->text('keterangan')->nullable();
            $table->text('spesifikasi_teknis')->nullable();
            $table->string('kondisi')->default('baik');
            $table->string('lokasi_penempatan')->nullable();
            $table->string('pic_customer')->nullable();
            $table->string('pic_msa')->nullable();

            $table->date('tanggal_investasi');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index(['customer_id', 'status']);
            $table->index(['kategori']);
            $table->index(['tanggal_install']);
            $table->index(['periode_kso_mulai', 'periode_kso_berakhir']);
            $table->index('unique_id');
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
