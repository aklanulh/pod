<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomerSchedule;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;

class CustomerScheduleSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->info('No customers or products found. Please run customer and product seeders first.');
            return;
        }

        $schedules = [
            // Overdue schedules
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Follow up pembelian rutin',
                'scheduled_date' => Carbon::parse('2025-11-16')->toDateString(),
                'status' => 'pending',
                'is_recurring' => true,
                'recurring_days' => 14,
                'notes' => 'Customer prioritas tinggi'
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Reminder pembelian urgent',
                'scheduled_date' => Carbon::parse('2025-11-18')->toDateString(),
                'status' => 'pending',
                'is_recurring' => false,
                'notes' => 'Hubungi sebelum jam 3 sore'
            ],

            // Due today
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Follow up customer loyal',
                'scheduled_date' => Carbon::parse('2025-11-19')->toDateString(),
                'status' => 'pending',
                'is_recurring' => true,
                'recurring_days' => 30,
                'notes' => 'Customer loyal, berikan diskon 5%'
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Kontak via WhatsApp',
                'scheduled_date' => Carbon::parse('2025-11-19')->toDateString(),
                'status' => 'pending',
                'is_recurring' => true,
                'recurring_days' => 30,
                'notes' => 'Biasanya pesan via WhatsApp'
            ],

            // Due this week
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Konfirmasi spesifikasi produk',
                'scheduled_date' => Carbon::parse('2025-11-21')->toDateString(),
                'status' => 'pending',
                'is_recurring' => false,
                'notes' => 'Konfirmasi ukuran yang dibutuhkan'
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Urgent - Kalibrasi alat',
                'scheduled_date' => Carbon::parse('2025-11-23')->toDateString(),
                'status' => 'pending',
                'is_recurring' => true,
                'recurring_days' => 7,
                'notes' => 'Urgent untuk kalibrasi alat'
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Jadwal pengiriman fleksibel',
                'scheduled_date' => Carbon::parse('2025-11-24')->toDateString(),
                'status' => 'pending',
                'is_recurring' => false,
                'notes' => 'Fleksibel untuk tanggal pengiriman'
            ],

            // Future schedules
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Negosiasi harga customer besar',
                'scheduled_date' => Carbon::now()->addDays(10)->toDateString(),
                'status' => 'pending',
                'is_recurring' => true,
                'recurring_days' => 90,
                'notes' => 'Customer besar, nego harga dimungkinkan'
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Jadwal demo dan training',
                'scheduled_date' => Carbon::now()->addDays(15)->toDateString(),
                'status' => 'pending',
                'is_recurring' => false,
                'notes' => 'Perlu demo dan training'
            ],

            // Some completed schedules
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Pesanan telah diproses',
                'scheduled_date' => Carbon::now()->subDays(7)->toDateString(),
                'status' => 'completed',
                'is_recurring' => true,
                'recurring_days' => 7,
                'notes' => 'Sudah diproses dan dikirim'
            ],
            [
                'customer_id' => $customers->random()->id,
                'product_id' => $products->random()->id,
                'title' => 'Menunggu konfirmasi customer',
                'scheduled_date' => Carbon::now()->subDays(5)->toDateString(),
                'status' => 'notified',
                'is_recurring' => true,
                'recurring_days' => 30,
                'notes' => 'Customer sudah dihubungi, menunggu konfirmasi'
            ]
        ];

        foreach ($schedules as $schedule) {
            CustomerSchedule::create($schedule);
        }

        $this->command->info('Customer schedules seeded successfully!');
    }
}
