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
            $table->string('unique_id', 8)->unique()->nullable()->after('id');
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('nama_alat');

            // Equipment Details
            $table->string('brand')->nullable()->after('nama_alat');
            $table->string('model')->nullable()->after('brand');
            $table->string('serial_number')->nullable()->after('model');
            $table->string('no_registrasi')->nullable()->after('serial_number');

            // Categories (changed from ENUM to VARCHAR)
            $table->string('kategori')->nullable()->after('no_registrasi');

            // Installation & Warranty
            $table->date('tanggal_install')->nullable()->after('tanggal_investasi');
            $table->date('garansi_mulai')->nullable()->after('tanggal_install');
            $table->date('garansi_berakhir')->nullable()->after('garansi_mulai');

            // KSO Period
            $table->date('periode_kso_mulai')->nullable()->after('garansi_berakhir');
            $table->date('periode_kso_berakhir')->nullable()->after('periode_kso_mulai');
            $table->integer('durasi_kso_bulan')->nullable()->after('periode_kso_berakhir');

            // Financial
            $table->decimal('nilai_alat_utama', 15, 2);
            $table->decimal('nilai_sewa_bulanan', 15, 2)->nullable()->after('nilai_alat_utama');
            $table->decimal('deposit', 15, 2)->nullable()->after('nilai_sewa_bulanan');
            $table->boolean('butuh_komputer')->default(false);
            $table->decimal('total_pendukung', 15, 2)->default(0);
            $table->decimal('total_investasi', 15, 2);

            // Additional Details
            $table->text('keterangan')->nullable();
            $table->text('spesifikasi_teknis')->nullable()->after('keterangan');
            $table->string('kondisi')->default('baik')->after('spesifikasi_teknis');
            $table->string('lokasi_penempatan')->nullable()->after('kondisi');
            $table->string('pic_customer')->nullable()->after('lokasi_penempatan');
            $table->string('pic_msa')->nullable()->after('pic_customer');

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
