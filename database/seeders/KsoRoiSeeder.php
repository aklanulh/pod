<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\KsoItem;
use App\Models\KsoSupportItem;
use Carbon\Carbon;

class KsoRoiSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get existing customers or create sample ones
        $customers = Customer::all();

        if ($customers->isEmpty()) {
            // Create sample customers if none exist
            $customers = collect([
                Customer::create([
                    'name' => 'RS Siloam Hospitals',
                    'contact_person' => 'Dr. Ahmad Santoso',
                    'phone' => '021-5555-1001',
                    'email' => 'ahmad.santoso@siloam.co.id',
                    'address' => 'Jl. Siloam No. 6, Jakarta Barat'
                ]),
                Customer::create([
                    'name' => 'Lab Klinik Prodia',
                    'contact_person' => 'dr. Sari Wijaya',
                    'phone' => '021-5555-2002',
                    'email' => 'sari.wijaya@prodia.co.id',
                    'address' => 'Jl. Kramat Raya No. 150, Jakarta Pusat'
                ]),
                Customer::create([
                    'name' => 'RS Mayapada Hospital',
                    'contact_person' => 'Dr. Budi Hartono',
                    'phone' => '021-5555-3003',
                    'email' => 'budi.hartono@mayapada.co.id',
                    'address' => 'Jl. Lebak Bulus I No. 29, Jakarta Selatan'
                ]),
                Customer::create([
                    'name' => 'Puskesmas Tanah Abang',
                    'contact_person' => 'dr. Rina Sari',
                    'phone' => '021-5555-4004',
                    'email' => 'rina.sari@puskesmas.go.id',
                    'address' => 'Jl. Tanah Abang III No. 1, Jakarta Pusat'
                ])
            ]);
        }

        // KSO Items data with realistic medical equipment
        $ksoItemsData = [
            // RS Siloam Hospitals - High-end equipment
            [
                'customer' => $customers->where('name', 'RS Siloam Hospitals')->first() ?? $customers->first(),
                'nama_alat' => 'Hematology Analyzer Mindray BC-6800',
                'brand' => 'Mindray',
                'model' => 'BC-6800',
                'serial_number' => 'MDR-BC6800-2023-001',
                'no_registrasi' => 'REG-HEM-001-2023',
                'kategori' => 'hematologi',
                'nilai_alat_utama' => 450000000, // 450 juta
                'nilai_sewa_bulanan' => 15000000, // 15 juta per bulan
                'deposit' => 45000000, // 45 juta deposit
                'butuh_komputer' => true,
                'tanggal_investasi' => Carbon::parse('2024-05-19'),
                'tanggal_install' => Carbon::parse('2024-06-19'),
                'garansi_mulai' => Carbon::parse('2024-06-19'),
                'garansi_berakhir' => Carbon::parse('2026-06-19'), // 2 tahun garansi
                'periode_kso_mulai' => Carbon::parse('2024-06-19'),
                'periode_kso_berakhir' => Carbon::parse('2029-06-19'), // 5 tahun KSO
                'durasi_kso_bulan' => 60,
                'keterangan' => 'Alat analisa hematologi otomatis untuk pemeriksaan darah lengkap dengan throughput tinggi',
                'spesifikasi_teknis' => 'Throughput: 60 samples/hour, Parameters: 26 parameters + 3 histograms, Sample volume: 20μL',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab Hematologi Lantai 2',
                'pic_customer' => 'Dr. Ahmad Santoso',
                'pic_msa' => 'Ir. Budi Setiawan',
                'support_items' => [
                    ['nama_item' => 'Workstation PC Medical Grade', 'nilai_item' => 15000000, 'jumlah' => 1, 'spesifikasi' => 'Intel i7, 16GB RAM, SSD 512GB'],
                    ['nama_item' => 'Monitor LCD 24 inch Medical', 'nilai_item' => 8000000, 'jumlah' => 2, 'spesifikasi' => 'Medical grade display dengan color accuracy'],
                    ['nama_item' => 'UPS 2000VA Medical Grade', 'nilai_item' => 5000000, 'jumlah' => 1, 'spesifikasi' => 'Online UPS untuk peralatan medis'],
                    ['nama_item' => 'Printer Thermal Medical', 'nilai_item' => 3000000, 'jumlah' => 1, 'spesifikasi' => 'Thermal printer untuk hasil lab'],
                ]
            ],
            [
                'customer' => $customers->where('name', 'RS Siloam Hospitals')->first() ?? $customers->first(),
                'nama_alat' => 'Chemistry Analyzer Mindray BS-240',
                'brand' => 'Mindray',
                'model' => 'BS-240',
                'serial_number' => 'MDR-BS240-2023-002',
                'no_registrasi' => 'REG-KIM-002-2023',
                'kategori' => 'kimia_klinik',
                'nilai_alat_utama' => 380000000, // 380 juta
                'nilai_sewa_bulanan' => 12000000, // 12 juta per bulan
                'deposit' => 38000000, // 38 juta deposit
                'butuh_komputer' => true,
                'tanggal_investasi' => Carbon::parse('2024-08-19'),
                'tanggal_install' => Carbon::parse('2024-09-19'),
                'garansi_mulai' => Carbon::parse('2024-09-19'),
                'garansi_berakhir' => Carbon::parse('2026-09-19'), // 2 tahun garansi
                'periode_kso_mulai' => Carbon::parse('2024-09-19'),
                'periode_kso_berakhir' => Carbon::parse('2029-09-19'), // 5 tahun KSO
                'durasi_kso_bulan' => 60,
                'keterangan' => 'Alat analisa kimia klinik otomatis dengan reagent mindray',
                'spesifikasi_teknis' => 'Throughput: 200 tests/hour, Parameters: Comprehensive metabolic panel, Sample volume: 2-35μL',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab Kimia Klinik Lantai 1',
                'pic_customer' => 'Dr. Ahmad Santoso',
                'pic_msa' => 'Ir. Budi Setiawan',
                'support_items' => [
                    ['nama_item' => 'Workstation PC Medical Grade', 'nilai_item' => 15000000, 'jumlah' => 1, 'spesifikasi' => 'Intel i7, 16GB RAM, SSD 512GB'],
                    ['nama_item' => 'Monitor LCD 24 inch Medical', 'nilai_item' => 8000000, 'jumlah' => 1, 'spesifikasi' => 'Medical grade display'],
                    ['nama_item' => 'UPS 1500VA Medical Grade', 'nilai_item' => 4000000, 'jumlah' => 1, 'spesifikasi' => 'Online UPS untuk peralatan medis'],
                ]
            ],

            // Lab Klinik Prodia - Mid-range equipment
            [
                'customer' => $customers->where('name', 'Lab Klinik Prodia')->first() ?? $customers->skip(1)->first(),
                'nama_alat' => 'Blood Gas Analyzer ABL90 FLEX',
                'brand' => 'Radiometer',
                'model' => 'ABL90 FLEX',
                'serial_number' => 'RDM-ABL90-2024-003',
                'no_registrasi' => 'REG-GAS-003-2024',
                'kategori' => 'gas_darah',
                'nilai_alat_utama' => 520000000, // 520 juta
                'nilai_sewa_bulanan' => 18000000, // 18 juta per bulan
                'deposit' => 52000000, // 52 juta deposit
                'butuh_komputer' => false,
                'tanggal_investasi' => Carbon::parse('2024-11-19'),
                'tanggal_install' => Carbon::parse('2024-12-19'),
                'garansi_mulai' => Carbon::parse('2024-12-19'),
                'garansi_berakhir' => Carbon::parse('2026-12-19'), // 2 tahun garansi
                'periode_kso_mulai' => Carbon::parse('2024-12-19'),
                'periode_kso_berakhir' => Carbon::parse('2029-12-19'), // 5 tahun KSO
                'durasi_kso_bulan' => 60,
                'keterangan' => 'Alat analisa gas darah untuk pemeriksaan pH, pCO2, pO2, elektrolit',
                'spesifikasi_teknis' => 'Parameters: pH, pCO2, pO2, Na+, K+, Cl-, Ca2+, Glucose, Lactate, Sample volume: 65μL',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab IGD Lantai 1',
                'pic_customer' => 'dr. Sari Wijaya',
                'pic_msa' => 'Ir. Andi Pratama',
                'support_items' => [
                    ['nama_item' => 'UPS 1000VA Medical Grade', 'nilai_item' => 3000000, 'jumlah' => 1, 'spesifikasi' => 'Backup power untuk alat gas darah'],
                    ['nama_item' => 'Printer Thermal Portable', 'nilai_item' => 2500000, 'jumlah' => 1, 'spesifikasi' => 'Portable thermal printer'],
                ]
            ],
            [
                'customer' => $customers->where('name', 'Lab Klinik Prodia')->first() ?? $customers->skip(1)->first(),
                'nama_alat' => 'Coagulation Analyzer CoaguChek Pro II',
                'brand' => 'Roche',
                'model' => 'CoaguChek Pro II',
                'serial_number' => 'RCH-CCP2-2024-004',
                'no_registrasi' => 'REG-COA-004-2024',
                'kategori' => 'koagulasi',
                'nilai_alat_utama' => 180000000, // 180 juta
                'nilai_sewa_bulanan' => 6000000, // 6 juta per bulan
                'deposit' => 18000000, // 18 juta deposit
                'butuh_komputer' => true,
                'tanggal_investasi' => Carbon::now()->subMonths(10),
                'tanggal_install' => Carbon::now()->subMonths(9),
                'garansi_mulai' => Carbon::now()->subMonths(9),
                'garansi_berakhir' => Carbon::now()->addMonths(15), // 2 tahun garansi
                'periode_kso_mulai' => Carbon::now()->subMonths(9),
                'periode_kso_berakhir' => Carbon::now()->addMonths(51), // 5 tahun KSO
                'durasi_kso_bulan' => 60,
                'keterangan' => 'Alat pemeriksaan koagulasi darah untuk PT, APTT, INR',
                'spesifikasi_teknis' => 'Parameters: PT, APTT, INR, Fibrinogen, Sample volume: 10μL, Results in 1-3 minutes',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab Hemostasis Lantai 2',
                'pic_customer' => 'dr. Sari Wijaya',
                'pic_msa' => 'Ir. Andi Pratama',
                'support_items' => [
                    ['nama_item' => 'Mini PC Medical Grade', 'nilai_item' => 8000000, 'jumlah' => 1, 'spesifikasi' => 'Compact PC untuk lab kecil'],
                    ['nama_item' => 'Monitor LCD 19 inch', 'nilai_item' => 4000000, 'jumlah' => 1, 'spesifikasi' => 'Standard LCD monitor'],
                ]
            ],

            // RS Mayapada Hospital - Premium equipment
            [
                'customer' => $customers->where('name', 'RS Mayapada Hospital')->first() ?? $customers->skip(2)->first(),
                'nama_alat' => 'Microscope Olympus CX23 Binocular',
                'brand' => 'Olympus',
                'model' => 'CX23',
                'serial_number' => 'OLY-CX23-2024-005',
                'no_registrasi' => 'REG-MIC-005-2024',
                'kategori' => 'mikrobiologi',
                'nilai_alat_utama' => 25000000, // 25 juta
                'nilai_sewa_bulanan' => 800000, // 800K per bulan
                'deposit' => 2500000, // 2.5 juta deposit
                'butuh_komputer' => false,
                'tanggal_investasi' => Carbon::now()->subMonths(8),
                'tanggal_install' => Carbon::now()->subMonths(7),
                'garansi_mulai' => Carbon::now()->subMonths(7),
                'garansi_berakhir' => Carbon::now()->addMonths(17), // 2 tahun garansi
                'periode_kso_mulai' => Carbon::now()->subMonths(7),
                'periode_kso_berakhir' => Carbon::now()->addMonths(53), // 5 tahun KSO
                'durasi_kso_bulan' => 60,
                'keterangan' => 'Mikroskop binokular untuk pemeriksaan mikroskopis rutin',
                'spesifikasi_teknis' => 'Magnification: 40x-1000x, LED illumination, Binocular head, Plan achromat objectives',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab Mikrobiologi Lantai 2',
                'pic_customer' => 'Dr. Budi Hartono',
                'pic_msa' => 'Ir. Citra Dewi',
                'support_items' => [
                    ['nama_item' => 'Digital Camera Microscope', 'nilai_item' => 12000000, 'jumlah' => 1, 'spesifikasi' => 'Kamera digital untuk dokumentasi'],
                ]
            ],
            [
                'customer' => $customers->where('name', 'RS Mayapada Hospital')->first() ?? $customers->skip(2)->first(),
                'nama_alat' => 'Centrifuge Hettich EBA 270',
                'brand' => 'Hettich',
                'model' => 'EBA 270',
                'serial_number' => 'HTT-EBA270-2024-006',
                'no_registrasi' => 'REG-CEN-006-2024',
                'kategori' => 'preparasi_sampel',
                'nilai_alat_utama' => 15000000, // 15 juta
                'nilai_sewa_bulanan' => 500000, // 500K per bulan
                'deposit' => 1500000, // 1.5 juta deposit
                'butuh_komputer' => false,
                'tanggal_investasi' => Carbon::now()->subMonths(6),
                'tanggal_install' => Carbon::now()->subMonths(5),
                'garansi_mulai' => Carbon::now()->subMonths(5),
                'garansi_berakhir' => Carbon::now()->addMonths(19), // 2 tahun garansi
                'periode_kso_mulai' => Carbon::now()->subMonths(5),
                'periode_kso_berakhir' => Carbon::now()->addMonths(55), // 5 tahun KSO
                'durasi_kso_bulan' => 60,
                'keterangan' => 'Sentrifuge untuk pemisahan sampel darah dan urin',
                'spesifikasi_teknis' => 'Max speed: 12,000 rpm, Capacity: 24 tubes x 10ml, Timer: 1-99 minutes',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab Preparasi Sampel Lantai 1',
                'pic_customer' => 'Dr. Budi Hartono',
                'pic_msa' => 'Ir. Citra Dewi',
                'support_items' => [
                    ['nama_item' => 'UPS 500VA', 'nilai_item' => 1500000, 'jumlah' => 1, 'spesifikasi' => 'Basic UPS untuk centrifuge'],
                ]
            ],

            // Puskesmas Tanah Abang - Basic equipment
            [
                'customer' => $customers->where('name', 'Puskesmas Tanah Abang')->first() ?? $customers->skip(3)->first(),
                'nama_alat' => 'Microscope Basic Olympus CX22',
                'brand' => 'Olympus',
                'model' => 'CX22',
                'serial_number' => 'OLY-CX22-2024-007',
                'no_registrasi' => 'REG-MIC-007-2024',
                'kategori' => 'mikrobiologi',
                'nilai_alat_utama' => 18000000, // 18 juta
                'nilai_sewa_bulanan' => 600000, // 600K per bulan
                'deposit' => 1800000, // 1.8 juta deposit
                'butuh_komputer' => false,
                'tanggal_investasi' => Carbon::now()->subMonths(4),
                'tanggal_install' => Carbon::now()->subMonths(3),
                'garansi_mulai' => Carbon::now()->subMonths(3),
                'garansi_berakhir' => Carbon::now()->addMonths(21), // 2 tahun garansi
                'periode_kso_mulai' => Carbon::now()->subMonths(3),
                'periode_kso_berakhir' => Carbon::now()->addMonths(57), // 5 tahun KSO
                'durasi_kso_bulan' => 60,
                'keterangan' => 'Mikroskop dasar untuk puskesmas',
                'spesifikasi_teknis' => 'Magnification: 40x-400x, LED illumination, Monocular head, Achromat objectives',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab Puskesmas Lantai 1',
                'pic_customer' => 'dr. Rina Sari',
                'pic_msa' => 'Ir. Dedi Kurniawan',
                'support_items' => []
            ],
            [
                'customer' => $customers->where('name', 'Puskesmas Tanah Abang')->first() ?? $customers->skip(3)->first(),
                'nama_alat' => 'Centrifuge Mini Hettich EBA 200',
                'brand' => 'Hettich',
                'model' => 'EBA 200',
                'serial_number' => 'HTT-EBA200-2024-008',
                'no_registrasi' => 'REG-CEN-008-2024',
                'kategori' => 'preparasi_sampel',
                'nilai_alat_utama' => 8000000, // 8 juta
                'nilai_sewa_bulanan' => 300000, // 300K per bulan
                'deposit' => 800000, // 800K deposit
                'butuh_komputer' => false,
                'tanggal_investasi' => Carbon::now()->subMonths(3),
                'tanggal_install' => Carbon::now()->subMonths(2),
                'garansi_mulai' => Carbon::now()->subMonths(2),
                'garansi_berakhir' => Carbon::now()->addMonths(22), // 2 tahun garansi
                'periode_kso_mulai' => Carbon::now()->subMonths(2),
                'periode_kso_berakhir' => Carbon::now()->addMonths(58), // 5 tahun KSO
                'durasi_kso_bulan' => 60,
                'keterangan' => 'Centrifuge mini untuk puskesmas',
                'spesifikasi_teknis' => 'Max speed: 6,000 rpm, Capacity: 12 tubes x 10ml, Timer: 1-30 minutes',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab Puskesmas Lantai 1',
                'pic_customer' => 'dr. Rina Sari',
                'pic_msa' => 'Ir. Dedi Kurniawan',
                'support_items' => []
            ]
        ];

        // Create KSO Items
        foreach ($ksoItemsData as $itemData) {
            // Calculate total support value
            $totalPendukung = 0;
            foreach ($itemData['support_items'] as $supportItem) {
                $totalPendukung += $supportItem['nilai_item'] * $supportItem['jumlah'];
            }

            // Create KSO Item
            $ksoItem = KsoItem::create([
                'customer_id' => $itemData['customer']->id,
                'nama_alat' => $itemData['nama_alat'],
                'brand' => $itemData['brand'] ?? null,
                'model' => $itemData['model'] ?? null,
                'serial_number' => $itemData['serial_number'] ?? null,
                'no_registrasi' => $itemData['no_registrasi'] ?? null,
                'kategori' => $itemData['kategori'] ?? null,
                'nilai_alat_utama' => $itemData['nilai_alat_utama'],
                'nilai_sewa_bulanan' => $itemData['nilai_sewa_bulanan'] ?? null,
                'deposit' => $itemData['deposit'] ?? null,
                'butuh_komputer' => $itemData['butuh_komputer'],
                'total_pendukung' => $totalPendukung,
                'total_investasi' => $itemData['nilai_alat_utama'] + $totalPendukung,
                'keterangan' => $itemData['keterangan'],
                'spesifikasi_teknis' => $itemData['spesifikasi_teknis'] ?? null,
                'kondisi' => $itemData['kondisi'] ?? 'baik',
                'lokasi_penempatan' => $itemData['lokasi_penempatan'] ?? null,
                'pic_customer' => $itemData['pic_customer'] ?? null,
                'pic_msa' => $itemData['pic_msa'] ?? null,
                'tanggal_investasi' => $itemData['tanggal_investasi'],
                'tanggal_install' => $itemData['tanggal_install'] ?? null,
                'garansi_mulai' => $itemData['garansi_mulai'] ?? null,
                'garansi_berakhir' => $itemData['garansi_berakhir'] ?? null,
                'periode_kso_mulai' => $itemData['periode_kso_mulai'] ?? null,
                'periode_kso_berakhir' => $itemData['periode_kso_berakhir'] ?? null,
                'durasi_kso_bulan' => $itemData['durasi_kso_bulan'] ?? null,
                'status' => 'active'
            ]);

            // Create Support Items
            foreach ($itemData['support_items'] as $supportItem) {
                KsoSupportItem::create([
                    'kso_item_id' => $ksoItem->id,
                    'nama_item' => $supportItem['nama_item'],
                    'nilai_item' => $supportItem['nilai_item'],
                    'jumlah' => $supportItem['jumlah'],
                    'spesifikasi' => $supportItem['spesifikasi'] ?? null
                ]);
            }
        }

        $this->command->info('KSO ROI sample data created successfully!');

        // Display summary
        $totalInvestment = KsoItem::where('status', 'active')->sum('total_investasi');
        $totalItems = KsoItem::count();
        $totalCustomers = KsoItem::distinct('customer_id')->count();

        $this->command->info("Summary:");
        $this->command->info("- Total KSO Items: {$totalItems}");
        $this->command->info("- Total Customers with KSO: {$totalCustomers}");
        $this->command->info("- Total Investment: Rp " . number_format($totalInvestment, 0, ',', '.'));
    }
}
