<?php

namespace App\Console\Commands;

use App\Models\KsoItem;
use App\Models\KsoSupportItem;
use Illuminate\Console\Command;

class VerifyKsoSupportItems extends Command
{
    protected $signature = 'kso:verify-support-items';
    protected $description = 'Verify KSO support items (alat komputer & pendukung) in database';

    public function handle()
    {
        $this->info('🔍 Verifying KSO Support Items (Alat Komputer & Pendukung)...');
        $this->newLine();

        $items = KsoItem::with(['customer', 'supportItems'])->get();
        
        if ($items->isEmpty()) {
            $this->error('❌ No KSO items found in database!');
            return;
        }

        $totalSupportItems = KsoSupportItem::count();
        $this->info("📊 Found {$items->count()} KSO items and {$totalSupportItems} support items in database");
        $this->newLine();

        foreach ($items as $index => $item) {
            $this->info("🔧 Item " . ($index + 1) . ": {$item->nama_alat} ({$item->customer->name})");
            
            if ($item->supportItems->count() > 0) {
                $this->line("   💻 Support Items ({$item->supportItems->count()}):");
                
                $totalSupportValue = 0;
                foreach ($item->supportItems as $support) {
                    $totalValue = $support->nilai_item * $support->jumlah;
                    $totalSupportValue += $totalValue;
                    
                    $this->line("      • {$support->nama_item} ({$support->jumlah}x) - Rp " . number_format($totalValue, 0, ',', '.'));
                    if ($support->spesifikasi) {
                        $this->line("        Spec: {$support->spesifikasi}");
                    }
                }
                
                $this->line("   💰 Total Support Value: Rp " . number_format($totalSupportValue, 0, ',', '.'));
                $this->line("   📊 Database Total Pendukung: Rp " . number_format($item->total_pendukung, 0, ',', '.'));
                
                if ($totalSupportValue != $item->total_pendukung) {
                    $this->warn("   ⚠️  Mismatch! Calculated vs Database values differ");
                }
            } else {
                $this->line("   ❌ No support items found");
            }
            
            $this->newLine();
        }

        // Summary
        $itemsWithSupport = $items->filter(function($item) {
            return $item->supportItems->count() > 0;
        });

        $this->info("📈 Summary:");
        $this->info("   Total KSO Items: {$items->count()}");
        $this->info("   Items with Support: {$itemsWithSupport->count()}");
        $this->info("   Items without Support: " . ($items->count() - $itemsWithSupport->count()));
        $this->info("   Total Support Items: {$totalSupportItems}");
        
        if ($itemsWithSupport->count() == $items->count()) {
            $this->info("🎉 All KSO items have support items!");
        } else {
            $this->warn("⚠️  Some KSO items are missing support items.");
        }
    }
}
