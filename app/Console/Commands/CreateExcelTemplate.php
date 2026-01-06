<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CreateExcelTemplate extends Command
{
    protected $signature = 'create:excel-template';
    protected $description = 'Create Excel template for WMS data import';

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Product Categories
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('product_categories');
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('A2', 'Reagens Hematologi');
        $sheet->setCellValue('A3', 'Reagens Kimia Klinik');
        $sheet->setCellValue('A4', 'Consumables Lab');
        $sheet->setCellValue('A5', 'Alat Laboratorium');
        $sheet->setCellValue('A6', 'Instrumen Medis');

        // Sheet 2: Suppliers
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('suppliers');
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'email');
        $sheet->setCellValue('C1', 'phone');
        $sheet->setCellValue('D1', 'address');
        
        $sheet->setCellValue('A2', 'PT. Medika Jaya');
        $sheet->setCellValue('B2', 'sales@medikajaya.com');
        $sheet->setCellValue('C2', '021-5551234');
        $sheet->setCellValue('D2', 'Jl. Healthcare No. 10 Jakarta');
        
        $sheet->setCellValue('A3', 'PT. Lab Supplies');
        $sheet->setCellValue('B3', 'info@labsupplies.co.id');
        $sheet->setCellValue('C3', '021-5555678');
        $sheet->setCellValue('D3', 'Jl. Laboratory Center Blok A-15');

        // Sheet 3: Customers
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('customers');
        $sheet->setCellValue('A1', 'kso_code');
        $sheet->setCellValue('B1', 'name');
        $sheet->setCellValue('C1', 'email');
        $sheet->setCellValue('D1', 'phone');
        $sheet->setCellValue('E1', 'address');
        $sheet->setCellValue('F1', 'contact_person');
        
        $sheet->setCellValue('A2', 'CUST001');
        $sheet->setCellValue('B2', 'RS Siloam Hospitals');
        $sheet->setCellValue('C2', 'procurement@siloam.co.id');
        $sheet->setCellValue('D2', '021-5551111');
        $sheet->setCellValue('E2', 'Jl. Siloam Hospital No. 100 Jakarta');
        $sheet->setCellValue('F2', 'Dr. Budi Santoso');

        // Sheet 4: Products
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('products');
        $sheet->setCellValue('A1', 'code');
        $sheet->setCellValue('B1', 'name');
        $sheet->setCellValue('C1', 'description');
        $sheet->setCellValue('D1', 'category_id');
        $sheet->setCellValue('E1', 'stock');
        $sheet->setCellValue('F1', 'unit_price');
        $sheet->setCellValue('G1', 'unit');
        
        $sheet->setCellValue('A2', 'REA-001');
        $sheet->setCellValue('B2', 'Reagen CBC Hematology');
        $sheet->setCellValue('C2', 'Reagen untuk pemeriksaan darah lengkap');
        $sheet->setCellValue('D2', '1');
        $sheet->setCellValue('E2', '50');
        $sheet->setCellValue('F2', '2500000');
        $sheet->setCellValue('G2', 'kit');

        // Sheet 5: Stock Movements
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('stock_movements');
        $sheet->setCellValue('A1', 'reference_number');
        $sheet->setCellValue('B1', 'product_id');
        $sheet->setCellValue('C1', 'customer_id');
        $sheet->setCellValue('D1', 'quantity');
        $sheet->setCellValue('E1', 'unit_price');
        $sheet->setCellValue('F1', 'transaction_date');
        $sheet->setCellValue('G1', 'type');
        
        $sheet->setCellValue('A2', 'INV-2025-001');
        $sheet->setCellValue('B2', 'REA-001');
        $sheet->setCellValue('C2', 'CUST001');
        $sheet->setCellValue('D2', '2');
        $sheet->setCellValue('E2', '2500000');
        $sheet->setCellValue('F2', '2025-01-15');
        $sheet->setCellValue('G2', 'out');

        // Save the file
        $writer = new Xlsx($spreadsheet);
        $filename = database_path('templates/wms_data_template_valid.xlsx');
        
        // Create directory if not exists
        if (!is_dir(database_path('templates'))) {
            mkdir(database_path('templates'), 0755, true);
        }
        
        $writer->save($filename);
        
        $this->info("Excel template created successfully: {$filename}");
        $this->info("You can now open this file and fill in your data.");
        
        return 0;
    }
}
