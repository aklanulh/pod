<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use Carbon\Carbon;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products, suppliers, and customers
        $products = Product::all();
        $suppliers = Supplier::all();
        $customers = Customer::all();

        if ($products->isEmpty() || $suppliers->isEmpty() || $customers->isEmpty()) {
            $this->command->warn('Please run ProductSeeder, SupplierSeeder, and CustomerSeeder first!');
            return;
        }

        // Create Stock In transactions (last 6 months: May - November 2025)
        $this->createStockInTransactions($products, $suppliers);

        // Create Stock Out transactions (last 5 months: June - November 2025)
        $this->createStockOutTransactions($products, $customers);

        // Create Multi-Product Stock In transactions (1 supplier, multiple products in same day)
        $this->createMultiProductStockInTransactions($products, $suppliers);

        // Create Multi-Product Stock Out transactions (1 customer, multiple products in same day)
        $this->createMultiProductStockOutTransactions($products, $customers);

        $this->command->info('Stock movements seeded successfully!');
    }

    private function createStockInTransactions($products, $suppliers)
    {
        $stockInData = [
            // DS Diluent transactions (6 months history)
            [
                'product_name' => 'DS Diluent',
                'supplier_name' => 'PT. Kimia Farma',
                'transactions' => [
                    // May 2025
                    ['quantity' => 100, 'unit_price' => 115000, 'date' => '2025-05-05'],
                    ['quantity' => 75, 'unit_price' => 118000, 'date' => '2025-05-18'],
                    // May 2025
                    ['quantity' => 80, 'unit_price' => 120000, 'date' => '2025-05-03'],
                    ['quantity' => 60, 'unit_price' => 122000, 'date' => '2025-05-20'],
                    // June 2025
                    ['quantity' => 90, 'unit_price' => 123000, 'date' => '2025-06-08'],
                    ['quantity' => 70, 'unit_price' => 124000, 'date' => '2025-06-25'],
                    // July 2025
                    ['quantity' => 85, 'unit_price' => 124500, 'date' => '2025-07-10'],
                    ['quantity' => 65, 'unit_price' => 125000, 'date' => '2025-07-28'],
                    // August 2025
                    ['quantity' => 95, 'unit_price' => 125000, 'date' => '2025-08-12'],
                    ['quantity' => 55, 'unit_price' => 126000, 'date' => '2025-08-30'],
                    // October 2025
                    ['quantity' => 50, 'unit_price' => 125000, 'date' => '2025-10-01'],
                    ['quantity' => 30, 'unit_price' => 125000, 'date' => '2025-10-10'],
                    ['quantity' => 40, 'unit_price' => 128000, 'date' => '2025-10-15'],
                    // November 2025
                    ['quantity' => 45, 'unit_price' => 130000, 'date' => '2025-11-05'],
                    ['quantity' => 35, 'unit_price' => 132000, 'date' => '2025-11-19'],
                ]
            ],
            // SC Cal Plus transactions (6 months history)
            [
                'product_name' => 'SC Cal Plus',
                'supplier_name' => 'PT. Kimia Farma',
                'transactions' => [
                    // May 2025
                    ['quantity' => 40, 'unit_price' => 170000, 'date' => '2025-05-08'],
                    ['quantity' => 35, 'unit_price' => 172000, 'date' => '2025-05-22'],
                    // May 2025
                    ['quantity' => 30, 'unit_price' => 175000, 'date' => '2025-05-15'],
                    ['quantity' => 25, 'unit_price' => 176000, 'date' => '2025-05-28'],
                    // June 2025
                    ['quantity' => 35, 'unit_price' => 178000, 'date' => '2025-06-12'],
                    // July 2025
                    ['quantity' => 28, 'unit_price' => 179000, 'date' => '2025-07-05'],
                    ['quantity' => 32, 'unit_price' => 180000, 'date' => '2025-07-25'],
                    // August 2025
                    ['quantity' => 30, 'unit_price' => 182000, 'date' => '2025-08-08'],
                    // October 2025
                    ['quantity' => 25, 'unit_price' => 180000, 'date' => '2025-10-02'],
                    ['quantity' => 20, 'unit_price' => 185000, 'date' => '2025-10-12'],
                    // November 2025
                    ['quantity' => 28, 'unit_price' => 188000, 'date' => '2025-11-08'],
                    ['quantity' => 22, 'unit_price' => 190000, 'date' => '2025-11-18'],
                ]
            ],
            // Vicom Glucose Kit transactions (6 months history)
            [
                'product_name' => 'Vicom Glucose Kit',
                'supplier_name' => 'CV. Medika Jaya',
                'transactions' => [
                    // May 2025
                    ['quantity' => 20, 'unit_price' => 420000, 'date' => '2025-05-12'],
                    // May 2025
                    ['quantity' => 18, 'unit_price' => 430000, 'date' => '2025-05-10'],
                    ['quantity' => 22, 'unit_price' => 435000, 'date' => '2025-05-25'],
                    // June 2025
                    ['quantity' => 16, 'unit_price' => 440000, 'date' => '2025-06-18'],
                    // July 2025
                    ['quantity' => 20, 'unit_price' => 445000, 'date' => '2025-07-15'],
                    // August 2025
                    ['quantity' => 12, 'unit_price' => 450000, 'date' => '2025-08-20'],
                    // October 2025
                    ['quantity' => 15, 'unit_price' => 450000, 'date' => '2025-10-03'],
                    ['quantity' => 10, 'unit_price' => 465000, 'date' => '2025-10-13'],
                    // November 2025
                    ['quantity' => 18, 'unit_price' => 470000, 'date' => '2025-11-06'],
                    ['quantity' => 12, 'unit_price' => 475000, 'date' => '2025-11-16'],
                ]
            ],
            // Vicom Cholesterol Kit transactions
            [
                'product_name' => 'Vicom Cholesterol Kit',
                'supplier_name' => 'CV. Medika Jaya',
                'transactions' => [
                    // May 2025
                    ['quantity' => 15, 'unit_price' => 480000, 'date' => '2025-05-15'],
                    // May 2025
                    ['quantity' => 12, 'unit_price' => 485000, 'date' => '2025-05-08'],
                    // June 2025
                    ['quantity' => 18, 'unit_price' => 490000, 'date' => '2025-06-22'],
                    // July 2025
                    ['quantity' => 14, 'unit_price' => 495000, 'date' => '2025-07-18'],
                    // August 2025
                    ['quantity' => 16, 'unit_price' => 500000, 'date' => '2025-08-25'],
                    // October 2025
                    ['quantity' => 10, 'unit_price' => 505000, 'date' => '2025-10-08'],
                    // November 2025
                    ['quantity' => 14, 'unit_price' => 510000, 'date' => '2025-11-10'],
                    ['quantity' => 12, 'unit_price' => 515000, 'date' => '2025-11-19'],
                ]
            ],
            // Vicom Triglyceride Kit transactions
            [
                'product_name' => 'Vicom Triglyceride Kit',
                'supplier_name' => 'CV. Medika Jaya',
                'transactions' => [
                    // June 2025
                    ['quantity' => 12, 'unit_price' => 520000, 'date' => '2025-06-12'],
                    // July 2025
                    ['quantity' => 15, 'unit_price' => 525000, 'date' => '2025-07-05'],
                    // August 2025
                    ['quantity' => 10, 'unit_price' => 530000, 'date' => '2025-08-22'],
                    // September 2025
                    ['quantity' => 14, 'unit_price' => 535000, 'date' => '2025-09-15'],
                    // October 2025
                    ['quantity' => 8, 'unit_price' => 540000, 'date' => '2025-10-20'],
                    // November 2025
                    ['quantity' => 10, 'unit_price' => 545000, 'date' => '2025-11-12'],
                    ['quantity' => 6, 'unit_price' => 550000, 'date' => '2025-11-19'],
                ]
            ],
            // Terumo Syringe 3ml transactions (6 months history)
            [
                'product_name' => 'Terumo Syringe 3ml',
                'supplier_name' => 'PT. Alkes Indonesia',
                'transactions' => [
                    // May 2025
                    ['quantity' => 1000, 'unit_price' => 2300, 'date' => '2025-05-02'],
                    ['quantity' => 800, 'unit_price' => 2350, 'date' => '2025-05-20'],
                    // May 2025
                    ['quantity' => 1200, 'unit_price' => 2400, 'date' => '2025-05-05'],
                    ['quantity' => 600, 'unit_price' => 2420, 'date' => '2025-05-22'],
                    // June 2025
                    ['quantity' => 900, 'unit_price' => 2450, 'date' => '2025-06-10'],
                    ['quantity' => 700, 'unit_price' => 2470, 'date' => '2025-06-28'],
                    // July 2025
                    ['quantity' => 1100, 'unit_price' => 2480, 'date' => '2025-07-08'],
                    ['quantity' => 500, 'unit_price' => 2490, 'date' => '2025-07-30'],
                    // August 2025
                    ['quantity' => 800, 'unit_price' => 2500, 'date' => '2025-08-05'],
                    ['quantity' => 600, 'unit_price' => 2520, 'date' => '2025-08-22'],
                    // October 2025
                    ['quantity' => 500, 'unit_price' => 2500, 'date' => '2025-10-04'],
                    ['quantity' => 300, 'unit_price' => 2600, 'date' => '2025-10-14'],
                    ['quantity' => 400, 'unit_price' => 2550, 'date' => '2025-10-18'],
                    // November 2025
                    ['quantity' => 600, 'unit_price' => 2580, 'date' => '2025-11-07'],
                    ['quantity' => 450, 'unit_price' => 2620, 'date' => '2025-11-17'],
                ]
            ],
            // Terumo Syringe 5ml transactions
            [
                'product_name' => 'Terumo Syringe 5ml',
                'supplier_name' => 'PT. Alkes Indonesia',
                'transactions' => [
                    // May 2025
                    ['quantity' => 600, 'unit_price' => 3200, 'date' => '2025-05-10'],
                    // May 2025
                    ['quantity' => 500, 'unit_price' => 3250, 'date' => '2025-05-18'],
                    ['quantity' => 400, 'unit_price' => 3280, 'date' => '2025-05-30'],
                    // June 2025
                    ['quantity' => 550, 'unit_price' => 3300, 'date' => '2025-06-15'],
                    // July 2025
                    ['quantity' => 450, 'unit_price' => 3350, 'date' => '2025-07-12'],
                    // August 2025
                    ['quantity' => 500, 'unit_price' => 3400, 'date' => '2025-08-18'],
                    // October 2025
                    ['quantity' => 300, 'unit_price' => 3450, 'date' => '2025-10-25'],
                    // November 2025
                    ['quantity' => 400, 'unit_price' => 3480, 'date' => '2025-11-08'],
                    ['quantity' => 350, 'unit_price' => 3520, 'date' => '2025-11-18'],
                ]
            ],
            // Ansell Latex Gloves transactions (6 months history)
            [
                'product_name' => 'Ansell Latex Gloves',
                'supplier_name' => 'PT. Alkes Indonesia',
                'transactions' => [
                    // May 2025
                    ['quantity' => 200, 'unit_price' => 78000, 'date' => '2025-05-06'],
                    ['quantity' => 150, 'unit_price' => 80000, 'date' => '2025-05-25'],
                    // May 2025
                    ['quantity' => 180, 'unit_price' => 82000, 'date' => '2025-05-14'],
                    ['quantity' => 120, 'unit_price' => 83000, 'date' => '2025-05-28'],
                    // June 2025
                    ['quantity' => 160, 'unit_price' => 84000, 'date' => '2025-06-08'],
                    // July 2025
                    ['quantity' => 140, 'unit_price' => 85000, 'date' => '2025-07-20'],
                    ['quantity' => 110, 'unit_price' => 86000, 'date' => '2025-07-31'],
                    // August 2025
                    ['quantity' => 130, 'unit_price' => 86500, 'date' => '2025-08-10'],
                    // October 2025
                    ['quantity' => 100, 'unit_price' => 85000, 'date' => '2025-10-05'],
                    ['quantity' => 80, 'unit_price' => 87000, 'date' => '2025-10-16'],
                    // November 2025
                    ['quantity' => 120, 'unit_price' => 88000, 'date' => '2025-11-06'],
                    ['quantity' => 90, 'unit_price' => 89000, 'date' => '2025-11-17'],
                ]
            ],
            // Eppendorf Pipette Tips 10μl transactions (6 months history)
            [
                'product_name' => 'Eppendorf Pipette Tips 10μl',
                'supplier_name' => 'CV. Medika Jaya',
                'transactions' => [
                    // May 2025
                    ['quantity' => 80, 'unit_price' => 300000, 'date' => '2025-05-14'],
                    // May 2025
                    ['quantity' => 70, 'unit_price' => 305000, 'date' => '2025-05-06'],
                    ['quantity' => 60, 'unit_price' => 310000, 'date' => '2025-05-24'],
                    // June 2025
                    ['quantity' => 75, 'unit_price' => 312000, 'date' => '2025-06-20'],
                    // July 2025
                    ['quantity' => 55, 'unit_price' => 315000, 'date' => '2025-07-16'],
                    // August 2025
                    ['quantity' => 65, 'unit_price' => 318000, 'date' => '2025-08-28'],
                    // October 2025
                    ['quantity' => 50, 'unit_price' => 320000, 'date' => '2025-10-06'],
                    ['quantity' => 30, 'unit_price' => 325000, 'date' => '2025-10-17'],
                    // November 2025
                    ['quantity' => 45, 'unit_price' => 330000, 'date' => '2025-11-09'],
                    ['quantity' => 35, 'unit_price' => 335000, 'date' => '2025-11-19'],
                ]
            ],
            // Eppendorf Pipette Tips 200μl transactions
            [
                'product_name' => 'Eppendorf Pipette Tips 200μl',
                'supplier_name' => 'CV. Medika Jaya',
                'transactions' => [
                    // May 2025
                    ['quantity' => 60, 'unit_price' => 350000, 'date' => '2025-05-16'],
                    // May 2025
                    ['quantity' => 50, 'unit_price' => 355000, 'date' => '2025-05-11'],
                    // June 2025
                    ['quantity' => 55, 'unit_price' => 360000, 'date' => '2025-06-26'],
                    // July 2025
                    ['quantity' => 45, 'unit_price' => 365000, 'date' => '2025-07-14'],
                    // August 2025
                    ['quantity' => 40, 'unit_price' => 370000, 'date' => '2025-08-12'],
                    // October 2025
                    ['quantity' => 35, 'unit_price' => 375000, 'date' => '2025-10-22'],
                    // November 2025
                    ['quantity' => 40, 'unit_price' => 380000, 'date' => '2025-11-11'],
                    ['quantity' => 30, 'unit_price' => 385000, 'date' => '2025-11-19'],
                ]
            ],
            // Falcon Centrifuge Tubes 15ml transactions
            [
                'product_name' => 'Falcon Centrifuge Tubes 15ml',
                'supplier_name' => 'CV. Medika Jaya',
                'transactions' => [
                    // June 2025
                    ['quantity' => 100, 'unit_price' => 180000, 'date' => '2025-06-02'],
                    // July 2025
                    ['quantity' => 80, 'unit_price' => 185000, 'date' => '2025-07-14'],
                    // August 2025
                    ['quantity' => 90, 'unit_price' => 188000, 'date' => '2025-08-26'],
                    // September 2025
                    ['quantity' => 70, 'unit_price' => 190000, 'date' => '2025-09-16'],
                    // October 2025
                    ['quantity' => 60, 'unit_price' => 195000, 'date' => '2025-10-12'],
                    // November 2025
                    ['quantity' => 75, 'unit_price' => 200000, 'date' => '2025-11-07'],
                    ['quantity' => 55, 'unit_price' => 205000, 'date' => '2025-11-17'],
                ]
            ],
            // Falcon Centrifuge Tubes 50ml transactions
            [
                'product_name' => 'Falcon Centrifuge Tubes 50ml',
                'supplier_name' => 'CV. Medika Jaya',
                'transactions' => [
                    // June 2025
                    ['quantity' => 60, 'unit_price' => 280000, 'date' => '2025-06-16'],
                    // July 2025
                    ['quantity' => 50, 'unit_price' => 285000, 'date' => '2025-07-30'],
                    // August 2025
                    ['quantity' => 55, 'unit_price' => 290000, 'date' => '2025-08-08'],
                    // September 2025
                    ['quantity' => 45, 'unit_price' => 295000, 'date' => '2025-09-24'],
                    // October 2025
                    ['quantity' => 40, 'unit_price' => 300000, 'date' => '2025-10-28'],
                    // November 2025
                    ['quantity' => 50, 'unit_price' => 310000, 'date' => '2025-11-10'],
                    ['quantity' => 35, 'unit_price' => 315000, 'date' => '2025-11-19'],
                ]
            ],
            // Eppendorf Pipette 10-100μl transactions
            [
                'product_name' => 'Eppendorf Pipette 10-100μl',
                'supplier_name' => 'PT. Alkes Indonesia',
                'transactions' => [
                    // May 2025
                    ['quantity' => 5, 'unit_price' => 2800000, 'date' => '2025-05-28'],
                    // July 2025
                    ['quantity' => 3, 'unit_price' => 2850000, 'date' => '2025-07-16'],
                    // September 2025
                    ['quantity' => 4, 'unit_price' => 2900000, 'date' => '2025-09-06'],
                    // October 2025
                    ['quantity' => 2, 'unit_price' => 2950000, 'date' => '2025-10-18'],
                    // November 2025
                    ['quantity' => 3, 'unit_price' => 3000000, 'date' => '2025-11-14'],
                ]
            ],
            // Eppendorf Pipette 100-1000μl transactions
            [
                'product_name' => 'Eppendorf Pipette 100-1000μl',
                'supplier_name' => 'PT. Alkes Indonesia',
                'transactions' => [
                    // May 2025
                    ['quantity' => 4, 'unit_price' => 3200000, 'date' => '2025-05-30'],
                    // July 2025
                    ['quantity' => 3, 'unit_price' => 3250000, 'date' => '2025-07-18'],
                    // September 2025
                    ['quantity' => 2, 'unit_price' => 3300000, 'date' => '2025-09-14'],
                    // October 2025
                    ['quantity' => 3, 'unit_price' => 3350000, 'date' => '2025-10-26'],
                    // November 2025
                    ['quantity' => 2, 'unit_price' => 3400000, 'date' => '2025-11-15'],
                ]
            ],
            // Eppendorf Microcentrifuge transactions
            [
                'product_name' => 'Eppendorf Microcentrifuge',
                'supplier_name' => 'PT. Alkes Indonesia',
                'transactions' => [
                    // June 2025
                    ['quantity' => 2, 'unit_price' => 15000000, 'date' => '2025-06-20'],
                    // September 2025
                    ['quantity' => 1, 'unit_price' => 15500000, 'date' => '2025-09-02'],
                    // November 2025
                    ['quantity' => 1, 'unit_price' => 16000000, 'date' => '2025-11-12'],
                ]
            ],
        ];

        $stockInCounter = 1;

        foreach ($stockInData as $data) {
            $product = $products->where('name', $data['product_name'])->first();
            $supplier = $suppliers->where('name', $data['supplier_name'])->first();

            if (!$product || !$supplier) continue;

            foreach ($data['transactions'] as $transaction) {
                $stockBefore = $product->current_stock ?? 0;
                $stockAfter = $stockBefore + $transaction['quantity'];

                StockMovement::create([
                    'reference_number' => 'SI-' . date('Ymd', strtotime($transaction['date'])) . '-' . str_pad($stockInCounter, 3, '0', STR_PAD_LEFT),
                    'order_number' => 'PO-' . date('Ymd', strtotime($transaction['date'])) . '-' . str_pad($stockInCounter, 3, '0', STR_PAD_LEFT),
                    'invoice_number' => 'INV-' . date('Ymd', strtotime($transaction['date'])) . '-' . str_pad($stockInCounter, 3, '0', STR_PAD_LEFT),
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $transaction['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'unit_price' => $transaction['unit_price'],
                    'supplier_id' => $supplier->id,
                    'customer_id' => null,
                    'notes' => null,
                    'transaction_date' => Carbon::parse($transaction['date']),
                    'created_at' => Carbon::parse($transaction['date']),
                    'updated_at' => Carbon::parse($transaction['date']),
                ]);

                // Update product current stock
                $product->update(['current_stock' => $stockAfter]);
                $stockInCounter++;
            }
        }
    }

    private function createStockOutTransactions($products, $customers)
    {
        $stockOutData = [
            // DS Diluent sales (5 months history)
            [
                'product_name' => 'DS Diluent',
                'customer_name' => 'RS. Siloam Hospitals',
                'transactions' => [
                    // May 2025
                    ['quantity' => 25, 'unit_price' => 145000, 'date' => '2025-05-08'],
                    ['quantity' => 30, 'unit_price' => 148000, 'date' => '2025-05-25'],
                    // June 2025
                    ['quantity' => 20, 'unit_price' => 150000, 'date' => '2025-06-12'],
                    ['quantity' => 35, 'unit_price' => 152000, 'date' => '2025-06-28'],
                    // July 2025
                    ['quantity' => 28, 'unit_price' => 153000, 'date' => '2025-07-15'],
                    ['quantity' => 22, 'unit_price' => 155000, 'date' => '2025-07-30'],
                    // August 2025
                    ['quantity' => 32, 'unit_price' => 156000, 'date' => '2025-08-14'],
                    ['quantity' => 18, 'unit_price' => 158000, 'date' => '2025-08-28'],
                    // September 2025
                    ['quantity' => 15, 'unit_price' => 150000, 'date' => '2025-09-07'],
                    ['quantity' => 20, 'unit_price' => 155000, 'date' => '2025-09-18'],
                ]
            ],
            // DS Diluent sales to different customer
            [
                'product_name' => 'DS Diluent',
                'customer_name' => 'Lab Klinik Prodia',
                'transactions' => [
                    // June 2025
                    ['quantity' => 15, 'unit_price' => 149000, 'date' => '2025-06-15'],
                    // July 2025
                    ['quantity' => 18, 'unit_price' => 151000, 'date' => '2025-07-08'],
                    // August 2025
                    ['quantity' => 12, 'unit_price' => 154000, 'date' => '2025-08-18'],
                    // September 2025
                    ['quantity' => 10, 'unit_price' => 157000, 'date' => '2025-09-24'],
                ]
            ],
            // SC Cal Plus sales (5 months history)
            [
                'product_name' => 'SC Cal Plus',
                'customer_name' => 'Lab Klinik Prodia',
                'transactions' => [
                    // May 2025
                    ['quantity' => 12, 'unit_price' => 210000, 'date' => '2025-05-18'],
                    ['quantity' => 15, 'unit_price' => 212000, 'date' => '2025-05-30'],
                    // June 2025
                    ['quantity' => 10, 'unit_price' => 215000, 'date' => '2025-06-20'],
                    // July 2025
                    ['quantity' => 14, 'unit_price' => 218000, 'date' => '2025-07-12'],
                    ['quantity' => 8, 'unit_price' => 220000, 'date' => '2025-07-28'],
                    // August 2025
                    ['quantity' => 12, 'unit_price' => 222000, 'date' => '2025-08-10'],
                    // September 2025
                    ['quantity' => 10, 'unit_price' => 220000, 'date' => '2025-09-08'],
                    ['quantity' => 8, 'unit_price' => 225000, 'date' => '2025-09-19'],
                ]
            ],
            // SC Cal Plus sales to different customer
            [
                'product_name' => 'SC Cal Plus',
                'customer_name' => 'Puskesmas Tanah Abang',
                'transactions' => [
                    // June 2025
                    ['quantity' => 6, 'unit_price' => 213000, 'date' => '2025-06-25'],
                    // August 2025
                    ['quantity' => 8, 'unit_price' => 219000, 'date' => '2025-08-22'],
                    // September 2025
                    ['quantity' => 5, 'unit_price' => 223000, 'date' => '2025-09-26'],
                ]
            ],
            // Vicom Glucose Kit sales (5 months history)
            [
                'product_name' => 'Vicom Glucose Kit',
                'customer_name' => 'Lab Klinik Prodia',
                'transactions' => [
                    // May 2025
                    ['quantity' => 8, 'unit_price' => 520000, 'date' => '2025-05-14'],
                    ['quantity' => 6, 'unit_price' => 525000, 'date' => '2025-05-28'],
                    // June 2025
                    ['quantity' => 7, 'unit_price' => 530000, 'date' => '2025-06-22'],
                    // July 2025
                    ['quantity' => 5, 'unit_price' => 535000, 'date' => '2025-07-18'],
                    // August 2025
                    ['quantity' => 6, 'unit_price' => 540000, 'date' => '2025-08-25'],
                    // September 2025
                    ['quantity' => 5, 'unit_price' => 550000, 'date' => '2025-09-09'],
                    ['quantity' => 3, 'unit_price' => 565000, 'date' => '2025-09-21'],
                ]
            ],
            // Vicom Cholesterol Kit sales
            [
                'product_name' => 'Vicom Cholesterol Kit',
                'customer_name' => 'Lab Klinik Prodia',
                'transactions' => [
                    // May 2025
                    ['quantity' => 4, 'unit_price' => 570000, 'date' => '2025-05-22'],
                    // June 2025
                    ['quantity' => 6, 'unit_price' => 580000, 'date' => '2025-06-18'],
                    // July 2025
                    ['quantity' => 5, 'unit_price' => 590000, 'date' => '2025-07-25'],
                    // August 2025
                    ['quantity' => 4, 'unit_price' => 600000, 'date' => '2025-08-20'],
                    // September 2025
                    ['quantity' => 3, 'unit_price' => 610000, 'date' => '2025-09-15'],
                ]
            ],
            // Vicom Triglyceride Kit sales
            [
                'product_name' => 'Vicom Triglyceride Kit',
                'customer_name' => 'Lab Klinik Prodia',
                'transactions' => [
                    // June 2025
                    ['quantity' => 3, 'unit_price' => 620000, 'date' => '2025-06-10'],
                    // July 2025
                    ['quantity' => 4, 'unit_price' => 630000, 'date' => '2025-07-28'],
                    // August 2025
                    ['quantity' => 2, 'unit_price' => 640000, 'date' => '2025-08-18'],
                    // September 2025
                    ['quantity' => 3, 'unit_price' => 650000, 'date' => '2025-09-22'],
                ]
            ],
            // Terumo Syringe 3ml sales (5 months history)
            [
                'product_name' => 'Terumo Syringe 3ml',
                'customer_name' => 'RS. Siloam Hospitals',
                'transactions' => [
                    // May 2025
                    ['quantity' => 400, 'unit_price' => 2900, 'date' => '2025-05-10'],
                    ['quantity' => 350, 'unit_price' => 2950, 'date' => '2025-05-25'],
                    // June 2025
                    ['quantity' => 300, 'unit_price' => 3000, 'date' => '2025-06-15'],
                    ['quantity' => 250, 'unit_price' => 3050, 'date' => '2025-06-30'],
                    // July 2025
                    ['quantity' => 380, 'unit_price' => 3100, 'date' => '2025-07-14'],
                    ['quantity' => 220, 'unit_price' => 3150, 'date' => '2025-07-28'],
                    // August 2025
                    ['quantity' => 320, 'unit_price' => 3180, 'date' => '2025-08-12'],
                    ['quantity' => 180, 'unit_price' => 3200, 'date' => '2025-08-26'],
                    // September 2025
                    ['quantity' => 200, 'unit_price' => 3200, 'date' => '2025-09-11'],
                    ['quantity' => 150, 'unit_price' => 3300, 'date' => '2025-09-22'],
                ]
            ],
            // Terumo Syringe 3ml sales to different customers
            [
                'product_name' => 'Terumo Syringe 3ml',
                'customer_name' => 'Puskesmas Tanah Abang',
                'transactions' => [
                    // June 2025
                    ['quantity' => 150, 'unit_price' => 2980, 'date' => '2025-06-18'],
                    // July 2025
                    ['quantity' => 200, 'unit_price' => 3080, 'date' => '2025-07-20'],
                    // August 2025
                    ['quantity' => 120, 'unit_price' => 3120, 'date' => '2025-08-15'],
                    // September 2025
                    ['quantity' => 100, 'unit_price' => 3250, 'date' => '2025-09-28'],
                ]
            ],
            // Terumo Syringe 5ml sales
            [
                'product_name' => 'Terumo Syringe 5ml',
                'customer_name' => 'RS. Siloam Hospitals',
                'transactions' => [
                    // May 2025
                    ['quantity' => 200, 'unit_price' => 3900, 'date' => '2025-05-20'],
                    // June 2025
                    ['quantity' => 180, 'unit_price' => 4000, 'date' => '2025-06-25'],
                    // July 2025
                    ['quantity' => 150, 'unit_price' => 4100, 'date' => '2025-07-22'],
                    // August 2025
                    ['quantity' => 120, 'unit_price' => 4200, 'date' => '2025-08-28'],
                    // September 2025
                    ['quantity' => 100, 'unit_price' => 4300, 'date' => '2025-09-20'],
                ]
            ],
            // Ansell Latex Gloves sales (5 months history)
            [
                'product_name' => 'Ansell Latex Gloves',
                'customer_name' => 'Puskesmas Tanah Abang',
                'transactions' => [
                    // May 2025
                    ['quantity' => 50, 'unit_price' => 98000, 'date' => '2025-05-12'],
                    ['quantity' => 40, 'unit_price' => 100000, 'date' => '2025-05-28'],
                    // June 2025
                    ['quantity' => 45, 'unit_price' => 102000, 'date' => '2025-06-14'],
                    // July 2025
                    ['quantity' => 35, 'unit_price' => 104000, 'date' => '2025-07-18'],
                    ['quantity' => 30, 'unit_price' => 106000, 'date' => '2025-07-30'],
                    // August 2025
                    ['quantity' => 40, 'unit_price' => 108000, 'date' => '2025-08-15'],
                    // September 2025
                    ['quantity' => 30, 'unit_price' => 105000, 'date' => '2025-09-23'],
                    ['quantity' => 25, 'unit_price' => 108000, 'date' => '2025-09-25'],
                ]
            ],
            // Ansell Latex Gloves sales to different customers
            [
                'product_name' => 'Ansell Latex Gloves',
                'customer_name' => 'RS. Siloam Hospitals',
                'transactions' => [
                    // June 2025
                    ['quantity' => 25, 'unit_price' => 101000, 'date' => '2025-06-22'],
                    // August 2025
                    ['quantity' => 20, 'unit_price' => 107000, 'date' => '2025-08-25'],
                    // September 2025
                    ['quantity' => 15, 'unit_price' => 109000, 'date' => '2025-09-30'],
                ]
            ],
            // Eppendorf Pipette Tips 10μl sales
            [
                'product_name' => 'Eppendorf Pipette Tips 10μl',
                'customer_name' => 'Lab Klinik Prodia',
                'transactions' => [
                    // May 2025
                    ['quantity' => 20, 'unit_price' => 380000, 'date' => '2025-05-15'],
                    // June 2025
                    ['quantity' => 25, 'unit_price' => 390000, 'date' => '2025-06-28'],
                    // July 2025
                    ['quantity' => 18, 'unit_price' => 400000, 'date' => '2025-07-25'],
                    // August 2025
                    ['quantity' => 22, 'unit_price' => 410000, 'date' => '2025-08-20'],
                    // September 2025
                    ['quantity' => 15, 'unit_price' => 420000, 'date' => '2025-09-18'],
                ]
            ],
            // Eppendorf Pipette Tips 200μl sales
            [
                'product_name' => 'Eppendorf Pipette Tips 200μl',
                'customer_name' => 'Lab Klinik Prodia',
                'transactions' => [
                    // May 2025
                    ['quantity' => 15, 'unit_price' => 430000, 'date' => '2025-05-25'],
                    // June 2025
                    ['quantity' => 18, 'unit_price' => 440000, 'date' => '2025-06-20'],
                    // July 2025
                    ['quantity' => 12, 'unit_price' => 450000, 'date' => '2025-07-30'],
                    // August 2025
                    ['quantity' => 14, 'unit_price' => 460000, 'date' => '2025-08-22'],
                    // September 2025
                    ['quantity' => 10, 'unit_price' => 470000, 'date' => '2025-09-25'],
                ]
            ],
            // Falcon Centrifuge Tubes 15ml sales
            [
                'product_name' => 'Falcon Centrifuge Tubes 15ml',
                'customer_name' => 'Lab Klinik Prodia',
                'transactions' => [
                    // June 2025
                    ['quantity' => 30, 'unit_price' => 220000, 'date' => '2025-06-18'],
                    // July 2025
                    ['quantity' => 25, 'unit_price' => 230000, 'date' => '2025-07-22'],
                    // August 2025
                    ['quantity' => 20, 'unit_price' => 240000, 'date' => '2025-08-28'],
                    // September 2025
                    ['quantity' => 18, 'unit_price' => 250000, 'date' => '2025-09-20'],
                ]
            ],
            // Falcon Centrifuge Tubes 50ml sales
            [
                'product_name' => 'Falcon Centrifuge Tubes 50ml',
                'customer_name' => 'Lab Klinik Prodia',
                'transactions' => [
                    // June 2025
                    ['quantity' => 20, 'unit_price' => 350000, 'date' => '2025-06-25'],
                    // July 2025
                    ['quantity' => 15, 'unit_price' => 360000, 'date' => '2025-07-28'],
                    // August 2025
                    ['quantity' => 12, 'unit_price' => 370000, 'date' => '2025-08-30'],
                    // September 2025
                    ['quantity' => 10, 'unit_price' => 380000, 'date' => '2025-09-25'],
                ]
            ],
        ];

        $stockOutCounter = 1;

        foreach ($stockOutData as $data) {
            $product = $products->where('name', $data['product_name'])->first();
            $customer = $customers->where('name', $data['customer_name'])->first();

            if (!$product || !$customer) continue;

            foreach ($data['transactions'] as $transaction) {
                $stockBefore = $product->current_stock ?? 0;

                // Only create transaction if we have enough stock
                if ($stockBefore >= $transaction['quantity']) {
                    $stockAfter = $stockBefore - $transaction['quantity'];

                    StockMovement::create([
                        'reference_number' => 'SO-' . date('Ymd', strtotime($transaction['date'])) . '-' . str_pad($stockOutCounter, 3, '0', STR_PAD_LEFT),
                        'order_number' => 'SO-' . date('Ymd', strtotime($transaction['date'])) . '-' . str_pad($stockOutCounter, 3, '0', STR_PAD_LEFT),
                        'invoice_number' => 'INV-OUT-' . date('Ymd', strtotime($transaction['date'])) . '-' . str_pad($stockOutCounter, 3, '0', STR_PAD_LEFT),
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $transaction['quantity'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'unit_price' => $transaction['unit_price'],
                        'supplier_id' => null,
                        'customer_id' => $customer->id,
                        'notes' => null,
                        'transaction_date' => Carbon::parse($transaction['date']),
                        'created_at' => Carbon::parse($transaction['date']),
                        'updated_at' => Carbon::parse($transaction['date']),
                    ]);

                    // Update product current stock
                    $product->update(['current_stock' => $stockAfter]);
                    $stockOutCounter++;
                }
            }
        }
    }

    private function createMultiProductStockInTransactions($products, $suppliers)
    {
        // Skenario: 1 supplier dalam 1 hari dengan beberapa produk
        $multiProductStockInData = [
            // PT. Kimia Farma - Pembelian besar-besaran tanggal 2025-10-01
            [
                'supplier_name' => 'PT. Kimia Farma',
                'date' => '2025-10-01',
                'products' => [
                    ['product_name' => 'DS Diluent', 'quantity' => 200, 'unit_price' => 125000],
                    ['product_name' => 'SC Cal Plus', 'quantity' => 50, 'unit_price' => 180000],
                ]
            ],
            // CV. Medika Jaya - Restok bulanan tanggal 2025-10-05
            [
                'supplier_name' => 'CV. Medika Jaya',
                'date' => '2025-10-05',
                'products' => [
                    ['product_name' => 'Vicom Glucose Kit', 'quantity' => 30, 'unit_price' => 450000],
                    ['product_name' => 'Vicom Cholesterol Kit', 'quantity' => 25, 'unit_price' => 505000],
                    ['product_name' => 'Vicom Triglyceride Kit', 'quantity' => 20, 'unit_price' => 540000],
                    ['product_name' => 'Eppendorf Pipette Tips 10μl', 'quantity' => 100, 'unit_price' => 320000],
                    ['product_name' => 'Eppendorf Pipette Tips 200μl', 'quantity' => 80, 'unit_price' => 375000],
                ]
            ],
            // PT. Alkes Indonesia - Pembelian alat kesehatan tanggal 2025-10-08
            [
                'supplier_name' => 'PT. Alkes Indonesia',
                'date' => '2025-10-08',
                'products' => [
                    ['product_name' => 'Terumo Syringe 3ml', 'quantity' => 2000, 'unit_price' => 2500],
                    ['product_name' => 'Terumo Syringe 5ml', 'quantity' => 1000, 'unit_price' => 3400],
                    ['product_name' => 'Ansell Latex Gloves', 'quantity' => 500, 'unit_price' => 85000],
                    ['product_name' => 'Eppendorf Pipette 10-100μl', 'quantity' => 10, 'unit_price' => 2900000],
                    ['product_name' => 'Eppendorf Pipette 100-1000μl', 'quantity' => 8, 'unit_price' => 3300000],
                ]
            ],
            // CV. Medika Jaya - Pembelian tubes tanggal 2025-10-12
            [
                'supplier_name' => 'CV. Medika Jaya',
                'date' => '2025-10-12',
                'products' => [
                    ['product_name' => 'Falcon Centrifuge Tubes 15ml', 'quantity' => 200, 'unit_price' => 190000],
                    ['product_name' => 'Falcon Centrifuge Tubes 50ml', 'quantity' => 150, 'unit_price' => 295000],
                ]
            ],
        ];

        $stockInCounter = 1000; // Start from 1000 to avoid conflicts

        foreach ($multiProductStockInData as $dayTransaction) {
            $supplier = $suppliers->where('name', $dayTransaction['supplier_name'])->first();
            if (!$supplier) continue;

            $batchNumber = 'BATCH-' . date('Ymd', strtotime($dayTransaction['date']));

            foreach ($dayTransaction['products'] as $productData) {
                $product = $products->where('name', $productData['product_name'])->first();
                if (!$product) continue;

                $stockBefore = $product->current_stock ?? 0;
                $stockAfter = $stockBefore + $productData['quantity'];

                StockMovement::create([
                    'reference_number' => 'SI-' . date('Ymd', strtotime($dayTransaction['date'])) . '-' . str_pad($stockInCounter, 3, '0', STR_PAD_LEFT),
                    'order_number' => 'PO-' . date('Ymd', strtotime($dayTransaction['date'])) . '-' . $batchNumber,
                    'invoice_number' => 'INV-' . date('Ymd', strtotime($dayTransaction['date'])) . '-' . $batchNumber,
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $productData['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'unit_price' => $productData['unit_price'],
                    'supplier_id' => $supplier->id,
                    'customer_id' => null,
                    'notes' => 'Pembelian batch ' . $batchNumber . ' - Multiple products from ' . $supplier->name,
                    'transaction_date' => Carbon::parse($dayTransaction['date']),
                    'created_at' => Carbon::parse($dayTransaction['date']),
                    'updated_at' => Carbon::parse($dayTransaction['date']),
                ]);

                // Update product current stock
                $product->update(['current_stock' => $stockAfter]);
                $stockInCounter++;
            }
        }
    }

    private function createMultiProductStockOutTransactions($products, $customers)
    {
        // Skenario: 1 customer dalam 1 hari dengan beberapa barang
        $multiProductStockOutData = [
            // RS. Siloam Hospitals - Pembelian besar untuk operasional tanggal 2025-10-03
            [
                'customer_name' => 'RS. Siloam Hospitals',
                'date' => '2025-10-03',
                'products' => [
                    ['product_name' => 'DS Diluent', 'quantity' => 50, 'unit_price' => 155000],
                    ['product_name' => 'Terumo Syringe 3ml', 'quantity' => 500, 'unit_price' => 3200],
                    ['product_name' => 'Terumo Syringe 5ml', 'quantity' => 200, 'unit_price' => 4300],
                    ['product_name' => 'Ansell Latex Gloves', 'quantity' => 100, 'unit_price' => 108000],
                ]
            ],
            // Lab Klinik Prodia - Restok kit laboratorium tanggal 2025-10-07
            [
                'customer_name' => 'Lab Klinik Prodia',
                'date' => '2025-10-07',
                'products' => [
                    ['product_name' => 'Vicom Glucose Kit', 'quantity' => 15, 'unit_price' => 550000],
                    ['product_name' => 'Vicom Cholesterol Kit', 'quantity' => 12, 'unit_price' => 610000],
                    ['product_name' => 'Vicom Triglyceride Kit', 'quantity' => 10, 'unit_price' => 650000],
                    ['product_name' => 'SC Cal Plus', 'quantity' => 20, 'unit_price' => 225000],
                    ['product_name' => 'Eppendorf Pipette Tips 10μl', 'quantity' => 40, 'unit_price' => 420000],
                    ['product_name' => 'Eppendorf Pipette Tips 200μl', 'quantity' => 30, 'unit_price' => 470000],
                ]
            ],
            // Puskesmas Tanah Abang - Pembelian rutin bulanan tanggal 2025-10-10
            [
                'customer_name' => 'Puskesmas Tanah Abang',
                'date' => '2025-10-10',
                'products' => [
                    ['product_name' => 'Terumo Syringe 3ml', 'quantity' => 300, 'unit_price' => 3250],
                    ['product_name' => 'Ansell Latex Gloves', 'quantity' => 80, 'unit_price' => 108000],
                    ['product_name' => 'SC Cal Plus', 'quantity' => 15, 'unit_price' => 223000],
                ]
            ],
            // Lab Klinik Prodia - Pembelian tubes dan consumables tanggal 2025-10-15
            [
                'customer_name' => 'Lab Klinik Prodia',
                'date' => '2025-10-15',
                'products' => [
                    ['product_name' => 'Falcon Centrifuge Tubes 15ml', 'quantity' => 50, 'unit_price' => 250000],
                    ['product_name' => 'Falcon Centrifuge Tubes 50ml', 'quantity' => 30, 'unit_price' => 380000],
                    ['product_name' => 'DS Diluent', 'quantity' => 25, 'unit_price' => 157000],
                ]
            ],
        ];

        $stockOutCounter = 1000; // Start from 1000 to avoid conflicts

        foreach ($multiProductStockOutData as $dayTransaction) {
            $customer = $customers->where('name', $dayTransaction['customer_name'])->first();
            if (!$customer) continue;

            $batchNumber = 'BATCH-OUT-' . date('Ymd', strtotime($dayTransaction['date']));

            foreach ($dayTransaction['products'] as $productData) {
                $product = $products->where('name', $productData['product_name'])->first();
                if (!$product) continue;

                $stockBefore = $product->current_stock ?? 0;

                // Only create transaction if we have enough stock
                if ($stockBefore >= $productData['quantity']) {
                    $stockAfter = $stockBefore - $productData['quantity'];

                    StockMovement::create([
                        'reference_number' => 'SO-' . date('Ymd', strtotime($dayTransaction['date'])) . '-' . str_pad($stockOutCounter, 3, '0', STR_PAD_LEFT),
                        'order_number' => 'SO-' . date('Ymd', strtotime($dayTransaction['date'])) . '-' . $batchNumber,
                        'invoice_number' => 'INV-OUT-' . date('Ymd', strtotime($dayTransaction['date'])) . '-' . $batchNumber,
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $productData['quantity'],
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'unit_price' => $productData['unit_price'],
                        'supplier_id' => null,
                        'customer_id' => $customer->id,
                        'notes' => 'Penjualan batch ' . $batchNumber . ' - Multiple products to ' . $customer->name,
                        'transaction_date' => Carbon::parse($dayTransaction['date']),
                        'created_at' => Carbon::parse($dayTransaction['date']),
                        'updated_at' => Carbon::parse($dayTransaction['date']),
                    ]);

                    // Update product current stock
                    $product->update(['current_stock' => $stockAfter]);
                    $stockOutCounter++;
                }
            }
        }
    }
}
