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
        Schema::table('kso_support_items', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('spesifikasi');
            $table->string('model')->nullable()->after('brand');
            $table->string('serial_number')->nullable()->after('model');
            $table->string('no_registrasi')->nullable()->after('serial_number');
            $table->date('tanggal_install')->nullable()->after('no_registrasi');
            $table->string('kategori')->nullable()->after('tanggal_install');
            $table->date('garansi_berakhir')->nullable()->after('kategori');
            $table->date('periode_kso_mulai')->nullable()->after('garansi_berakhir');
            $table->date('periode_kso_berakhir')->nullable()->after('periode_kso_mulai');
            $table->string('lokasi_penempatan')->nullable()->after('periode_kso_berakhir');
            $table->string('kondisi')->default('excellent')->after('lokasi_penempatan');
            $table->string('status')->default('active')->after('kondisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kso_support_items', function (Blueprint $table) {
            $table->dropColumn([
                'brand',
                'model', 
                'serial_number',
                'no_registrasi',
                'tanggal_install',
                'kategori',
                'garansi_berakhir',
                'periode_kso_mulai',
                'periode_kso_berakhir',
                'lokasi_penempatan',
                'kondisi',
                'status'
            ]);
        });
    }
};
