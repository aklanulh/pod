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
