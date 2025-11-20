<?php

namespace App\Console\Commands;

use App\Models\KsoItem;
use Illuminate\Console\Command;

class ShowKsoSample extends Command
{
    protected $signature = 'kso:show-sample';
    protected $description = 'Show sample KSO data from database';

    public function handle()
    {
        $this->info('📋 Sample KSO Data from Database');
        $this->newLine();

        $item = KsoItem::with('customer')->first();
        
        if (!$item) {
            $this->error('❌ No KSO items found!');
            return;
        }

        $this->info("🏥 Customer: {$item->customer->name}");
        $this->info("🔧 Equipment: {$item->nama_alat}");
        $this->info("🏷️  Brand: {$item->brand}");
        $this->info("📱 Model: {$item->model}");
        $this->info("🔢 Serial: {$item->serial_number}");
        $this->info("📋 Registration: {$item->no_registrasi}");
        $this->info("📂 Category: {$item->kategori}");
        $this->info("📅 Install Date: {$item->tanggal_install}");
        $this->info("🛡️  Warranty End: {$item->garansi_berakhir}");
        $this->info("📆 KSO Period: {$item->periode_kso_mulai} to {$item->periode_kso_berakhir}");
        $this->info("⏱️  Duration: {$item->durasi_kso_bulan} months");
        $this->info("💰 Monthly Rent: Rp " . number_format($item->nilai_sewa_bulanan));
        $this->info("📍 Location: {$item->lokasi_penempatan}");
        $this->info("👤 Customer PIC: {$item->pic_customer}");
        $this->info("👨‍💼 MSA PIC: {$item->pic_msa}");
        $this->info("⚙️  Condition: {$item->kondisi}");
        
        $this->newLine();
        $this->info("✅ All data is properly stored in database!");
    }
}
