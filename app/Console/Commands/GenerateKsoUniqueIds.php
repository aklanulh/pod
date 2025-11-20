<?php

namespace App\Console\Commands;

use App\Models\KsoItem;
use Illuminate\Console\Command;

class GenerateKsoUniqueIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kso:generate-unique-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique 6-8 digit IDs for all KSO items without unique_id';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Regenerating unique IDs for all KSO items...');
        $this->info('Format: YYMMSSSS (Tahun-Bulan-Urutan dalam setahun)');
        $this->newLine();

        // Get all KSO items sorted by created_at
        $items = KsoItem::orderBy('created_at')->get();

        if ($items->isEmpty()) {
            $this->info('✗ Tidak ada KSO items di database');
            return 0;
        }

        $count = 0;
        $this->withProgressBar($items, function ($item) use (&$count) {
            // Temporarily delete to avoid unique constraint
            $oldId = $item->unique_id;
            $item->update(['unique_id' => null]);

            // Generate new unique_id
            $newId = KsoItem::generateUniqueId();
            $item->update(['unique_id' => $newId]);

            $count++;
        });

        $this->newLine();
        $this->info("✓ Berhasil regenerate unique_id untuk {$count} KSO items");
        $this->newLine();

        // Show summary
        $this->info('Summary:');
        $items = KsoItem::orderBy('unique_id')->get();
        foreach ($items as $item) {
            $this->line("  • {$item->unique_id} - {$item->nama_alat}");
        }

        return 0;
    }
}
