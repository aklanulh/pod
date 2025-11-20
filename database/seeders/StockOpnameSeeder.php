<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockOpname;
use App\Models\StockOpnameDetail;
use App\Models\StockMovement;
use App\Models\Product;
use Carbon\Carbon;

class StockOpnameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('Please run ProductSeeder first!');
            return;
        }

        // Create stock opname transactions for the last 2 months
        $this->createStockOpnameTransactions($products);

        $this->command->info('Stock opname data seeded successfully!');
    }

    private function createStockOpnameTransactions($products)
    {
        // Stock Opname 1 - September 2025
        $opname1 = StockOpname::create([
            'opname_number' => 'OP-20250915-001',
            'opname_date' => Carbon::parse('2025-09-15'),
            'notes' => 'Stock opname bulanan September 2025',
            'status' => 'completed',
            'created_at' => Carbon::parse('2025-09-15'),
            'updated_at' => Carbon::parse('2025-09-15'),
        ]);

        // Stock Opname details for September
        $opnameDetails1 = [
            [
                'product_name' => 'DS Diluent',
                'system_stock' => 80,
                'physical_stock' => 78,
                'notes' => 'Selisih 2 unit, kemungkinan kerusakan kemasan'
            ],
            [
                'product_name' => 'SC Cal Plus',
                'system_stock' => 35,
                'physical_stock' => 35,
                'notes' => 'Stok sesuai sistem'
            ],
            [
                'product_name' => 'Vicom Glucose Kit',
                'system_stock' => 20,
                'physical_stock' => 19,
                'notes' => 'Selisih 1 unit, expired'
            ],
            [
                'product_name' => 'Terumo Syringe 3ml',
                'system_stock' => 600,
                'physical_stock' => 595,
                'notes' => 'Selisih 5 unit, kemasan rusak'
            ],
            [
                'product_name' => 'Ansell Latex Gloves',
                'system_stock' => 125,
                'physical_stock' => 125,
                'notes' => 'Stok sesuai sistem'
            ],
        ];

        foreach ($opnameDetails1 as $detail) {
            $product = $products->where('name', $detail['product_name'])->first();
            if (!$product) continue;

            $difference = $detail['physical_stock'] - $detail['system_stock'];

            StockOpnameDetail::create([
                'stock_opname_id' => $opname1->id,
                'product_id' => $product->id,
                'system_stock' => $detail['system_stock'],
                'physical_stock' => $detail['physical_stock'],
                'difference' => $difference,
                'notes' => $detail['notes']
            ]);

            // Create stock movement for adjustment if there's a difference
            if ($difference != 0) {
                StockMovement::create([
                    'reference_number' => 'OP-ADJ-' . $opname1->opname_number . '-' . $product->id,
                    'order_number' => null,
                    'invoice_number' => null,
                    'product_id' => $product->id,
                    'type' => 'opname',
                    'quantity' => abs($difference),
                    'stock_before' => $detail['system_stock'],
                    'stock_after' => $detail['physical_stock'],
                    'unit_price' => 0,
                    'supplier_id' => null,
                    'customer_id' => null,
                    'notes' => 'Penyesuaian stock opname: ' . $detail['notes'],
                    'transaction_date' => $opname1->opname_date,
                    'created_at' => $opname1->opname_date,
                    'updated_at' => $opname1->opname_date,
                ]);

                // Update product current stock
                $product->update(['current_stock' => $detail['physical_stock']]);
            }
        }

        // Stock Opname 2 - October 2025
        $opname2 = StockOpname::create([
            'opname_number' => 'OP-20251015-001',
            'opname_date' => Carbon::parse('2025-10-15'),
            'notes' => 'Stock opname bulanan Oktober 2025',
            'status' => 'completed',
            'created_at' => Carbon::parse('2025-10-15'),
            'updated_at' => Carbon::parse('2025-10-15'),
        ]);

        // Stock Opname details for October
        $opnameDetails2 = [
            [
                'product_name' => 'DS Diluent',
                'system_stock' => 98,
                'physical_stock' => 100,
                'notes' => 'Kelebihan 2 unit, ditemukan stok tersembunyi'
            ],
            [
                'product_name' => 'SC Cal Plus',
                'system_stock' => 27,
                'physical_stock' => 27,
                'notes' => 'Stok sesuai sistem'
            ],
            [
                'product_name' => 'Vicom Glucose Kit',
                'system_stock' => 26,
                'physical_stock' => 25,
                'notes' => 'Selisih 1 unit, kemasan rusak'
            ],
            [
                'product_name' => 'Terumo Syringe 3ml',
                'system_stock' => 945,
                'physical_stock' => 940,
                'notes' => 'Selisih 5 unit, hilang'
            ],
            [
                'product_name' => 'Ansell Latex Gloves',
                'system_stock' => 125,
                'physical_stock' => 123,
                'notes' => 'Selisih 2 unit, kemasan sobek'
            ],
            [
                'product_name' => 'Eppendorf Pipette Tips 10μl',
                'system_stock' => 80,
                'physical_stock' => 80,
                'notes' => 'Stok sesuai sistem'
            ],
        ];

        foreach ($opnameDetails2 as $detail) {
            $product = $products->where('name', $detail['product_name'])->first();
            if (!$product) continue;

            $difference = $detail['physical_stock'] - $detail['system_stock'];

            StockOpnameDetail::create([
                'stock_opname_id' => $opname2->id,
                'product_id' => $product->id,
                'system_stock' => $detail['system_stock'],
                'physical_stock' => $detail['physical_stock'],
                'difference' => $difference,
                'notes' => $detail['notes']
            ]);

            // Create stock movement for adjustment if there's a difference
            if ($difference != 0) {
                StockMovement::create([
                    'reference_number' => 'OP-ADJ-' . $opname2->opname_number . '-' . $product->id,
                    'order_number' => null,
                    'invoice_number' => null,
                    'product_id' => $product->id,
                    'type' => 'opname',
                    'quantity' => abs($difference),
                    'stock_before' => $detail['system_stock'],
                    'stock_after' => $detail['physical_stock'],
                    'unit_price' => 0,
                    'supplier_id' => null,
                    'customer_id' => null,
                    'notes' => 'Penyesuaian stock opname: ' . $detail['notes'],
                    'transaction_date' => $opname2->opname_date,
                    'created_at' => $opname2->opname_date,
                    'updated_at' => $opname2->opname_date,
                ]);

                // Update product current stock
                $product->update(['current_stock' => $detail['physical_stock']]);
            }
        }

        // Stock Opname 3 - Draft for November (in progress)
        $opname3 = StockOpname::create([
            'opname_number' => 'OP-20251119-001',
            'opname_date' => Carbon::parse('2025-11-19'),
            'notes' => 'Stock opname bulanan November 2025 (dalam proses)',
            'status' => 'draft',
            'created_at' => Carbon::parse('2025-11-19'),
            'updated_at' => Carbon::parse('2025-11-19'),
        ]);

        // Some draft details for November (not completed yet)
        $opnameDetails3 = [
            [
                'product_name' => 'DS Diluent',
                'system_stock' => 120,
                'physical_stock' => 0, // Not counted yet
                'notes' => 'Belum dihitung'
            ],
            [
                'product_name' => 'SC Cal Plus',
                'system_stock' => 19,
                'physical_stock' => 0, // Not counted yet
                'notes' => 'Belum dihitung'
            ],
        ];

        foreach ($opnameDetails3 as $detail) {
            $product = $products->where('name', $detail['product_name'])->first();
            if (!$product) continue;

            StockOpnameDetail::create([
                'stock_opname_id' => $opname3->id,
                'product_id' => $product->id,
                'system_stock' => $detail['system_stock'],
                'physical_stock' => $detail['physical_stock'],
                'difference' => 0, // Will be calculated when completed
                'notes' => $detail['notes']
            ]);
        }
    }
}
