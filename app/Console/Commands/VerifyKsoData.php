<?php

namespace App\Console\Commands;

use App\Models\KsoItem;
use Illuminate\Console\Command;

class VerifyKsoData extends Command
{
    protected $signature = 'kso:verify-data';
    protected $description = 'Verify that all KSO data is properly stored in database';

    public function handle()
    {
        $this->info('🔍 Verifying KSO Data in Database...');
        $this->newLine();

        $items = KsoItem::with('customer')->get();
        
        if ($items->isEmpty()) {
            $this->error('❌ No KSO items found in database!');
            $this->info('💡 Run: php artisan db:seed --class=KsoRoiSeeder');
            return;
        }

        $this->info("📊 Found {$items->count()} KSO items in database");
        $this->newLine();

        $fieldsToCheck = [
            'nama_alat' => 'Equipment Name',
            'brand' => 'Brand',
            'model' => 'Model', 
            'serial_number' => 'Serial Number',
            'no_registrasi' => 'Registration Number',
            'kategori' => 'Category',
            'tanggal_install' => 'Installation Date',
            'garansi_mulai' => 'Warranty Start',
            'garansi_berakhir' => 'Warranty End',
            'periode_kso_mulai' => 'KSO Period Start',
            'periode_kso_berakhir' => 'KSO Period End',
            'durasi_kso_bulan' => 'KSO Duration (months)',
            'nilai_sewa_bulanan' => 'Monthly Rental',
            'deposit' => 'Deposit',
            'lokasi_penempatan' => 'Location',
            'pic_customer' => 'Customer PIC',
            'pic_msa' => 'MSA PIC',
            'spesifikasi_teknis' => 'Technical Specs',
            'kondisi' => 'Condition'
        ];

        foreach ($items as $index => $item) {
            $this->info("🔧 Item " . ($index + 1) . ": {$item->nama_alat} ({$item->customer->name})");
            
            $missingFields = [];
            $presentFields = [];
            
            foreach ($fieldsToCheck as $field => $label) {
                if (empty($item->$field)) {
                    $missingFields[] = $label;
                } else {
                    $presentFields[] = $label;
                }
            }
            
            if (!empty($presentFields)) {
                $this->line("   ✅ Present: " . implode(', ', $presentFields));
            }
            
            if (!empty($missingFields)) {
                $this->line("   ⚠️  Missing: " . implode(', ', $missingFields));
            }
            
            $this->newLine();
        }

        // Summary
        $totalComplete = 0;
        $totalFields = count($fieldsToCheck);
        
        foreach ($items as $item) {
            $completeFields = 0;
            foreach ($fieldsToCheck as $field => $label) {
                if (!empty($item->$field)) {
                    $completeFields++;
                }
            }
            if ($completeFields == $totalFields) {
                $totalComplete++;
            }
        }

        $this->info("📈 Summary:");
        $this->info("   Total Items: {$items->count()}");
        $this->info("   Complete Items: {$totalComplete}");
        $this->info("   Completion Rate: " . round(($totalComplete / $items->count()) * 100, 1) . "%");
        
        if ($totalComplete == $items->count()) {
            $this->info("🎉 All KSO items have complete data!");
        } else {
            $this->warn("⚠️  Some items are missing data. Consider updating the seeder.");
        }
    }
}
