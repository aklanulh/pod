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
        Schema::table('kso_items', function (Blueprint $table) {
            // Equipment Details
            $table->string('brand')->nullable()->after('nama_alat');
            $table->string('model')->nullable()->after('brand');
            $table->string('serial_number')->nullable()->after('model');
            $table->string('no_registrasi')->nullable()->after('serial_number');
            
            // Categories
            $table->enum('kategori', [
                'hematologi', 
                'kimia_klinik', 
                'gas_darah', 
                'koagulasi', 
                'mikrobiologi', 
                'preparasi_sampel',
                'imaging',
                'monitoring',
                'lainnya'
            ])->nullable()->after('no_registrasi');
            
            // Installation & Warranty
            $table->date('tanggal_install')->nullable()->after('tanggal_investasi');
            $table->date('garansi_mulai')->nullable()->after('tanggal_install');
            $table->date('garansi_berakhir')->nullable()->after('garansi_mulai');
            
            // KSO Period
            $table->date('periode_kso_mulai')->nullable()->after('garansi_berakhir');
            $table->date('periode_kso_berakhir')->nullable()->after('periode_kso_mulai');
            $table->integer('durasi_kso_bulan')->nullable()->after('periode_kso_berakhir');
            
            // Additional Details
            $table->text('spesifikasi_teknis')->nullable()->after('keterangan');
            $table->string('kondisi')->default('baik')->after('spesifikasi_teknis');
            $table->string('lokasi_penempatan')->nullable()->after('kondisi');
            $table->string('pic_customer')->nullable()->after('lokasi_penempatan');
            $table->string('pic_msa')->nullable()->after('pic_customer');
            
            // Financial
            $table->decimal('nilai_sewa_bulanan', 15, 2)->nullable()->after('nilai_alat_utama');
            $table->decimal('deposit', 15, 2)->nullable()->after('nilai_sewa_bulanan');
            
            // Add indexes for better performance
            $table->index(['kategori']);
            $table->index(['tanggal_install']);
            $table->index(['periode_kso_mulai', 'periode_kso_berakhir']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kso_items', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'model', 
                'serial_number',
                'no_registrasi',
                'kategori',
                'tanggal_install',
                'garansi_mulai',
                'garansi_berakhir',
                'periode_kso_mulai',
                'periode_kso_berakhir',
                'durasi_kso_bulan',
                'spesifikasi_teknis',
                'kondisi',
                'lokasi_penempatan',
                'pic_customer',
                'pic_msa',
                'nilai_sewa_bulanan',
                'deposit'
            ]);
        });
    }
};
