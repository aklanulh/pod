<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DataImportTemplateController extends Controller
{
    public function stockMovementsTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'reference_number',
            'order_number',
            'invoice_number',
            'product_code',
            'type',
            'quantity',
            'unit_price',
            'include_tax',
            'discount_percent',
            'discount_amount',
            'payment_terms',
            'supplier_name',
            'customer_name',
            'notes',
            'transaction_date'
        ];

        // Add header row
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E3F2FD']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:O1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            [
                'SM-20250105-001',
                'PO-2025-001',
                'INV-2025-001',
                'LAB-001',
                'in',
                10,
                50000,
                'TRUE',
                0,
                0,
                30,
                'PT. Supplier Medika',
                '',
                'Sample stock in',
                '2025-01-05'
            ],
            [
                'SM-20250105-002',
                'SO-2025-001',
                'INV-2025-002',
                'LAB-001',
                'out',
                5,
                75000,
                'FALSE',
                10,
                37500,
                30,
                '',
                'RS. Test Hospital',
                'Sample stock out',
                '2025-01-05'
            ]
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Style data rows
        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A2:O3')->applyFromArray($dataStyle);

        // Auto-size columns
        foreach (range('A', 'O') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add instructions
        $sheet->setCellValue('P1', 'INSTRUCTIONS:');
        $sheet->getStyle('P1')->getFont()->setBold(true);

        $instructions = [
            'product_code: Must exist in products table',
            'type: in, out, or opname',
            'include_tax: TRUE or FALSE',
            'supplier_name: Required for type=in',
            'customer_name: Required for type=out',
            'transaction_date: YYYY-MM-DD format'
        ];

        $row = 2;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue('P' . $row, $instruction);
            $row++;
        }

        $filename = 'stock_movements_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function productsTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'code',
            'name',
            'description',
            'category_name',
            'unit',
            'price',
            'current_stock',
            'minimum_stock',
            'expired_date',
            'lot_number',
            'distribution_permit',
            'is_active',
            'migrated_to_product_code',
            'migration_notes'
        ];

        // Add header row
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E3F2FD']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            [
                'PRD-001',
                'Sample Product 1',
                'Sample description for product 1',
                'Medical Supplies',
                'PCS',
                50000,
                100,
                10,
                '2025-12-31',
                'LOT-001',
                'DIST-001',
                'TRUE',
                '',
                ''
            ],
            [
                'PRD-002',
                'Sample Product 2',
                'Sample description for product 2',
                'Laboratory Equipment',
                'UNIT',
                75000,
                50,
                5,
                '2025-06-30',
                'LOT-002',
                'DIST-002',
                'TRUE',
                '',
                ''
            ],
            [
                'PRD-003-OLD',
                'Old Product Name',
                'This product has been migrated',
                'Medical Supplies',
                'PCS',
                0,
                0,
                0,
                '',
                '',
                '',
                'FALSE',
                'PRD-003',
                'Migrated to new product name due to specification change'
            ]
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        // Style data rows
        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A2:N4')->applyFromArray($dataStyle);

        // Auto-size columns
        foreach (range('A', 'N') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add instructions
        $sheet->setCellValue('O1', 'INSTRUCTIONS:');
        $sheet->getStyle('O1')->getFont()->setBold(true);

        $instructions = [
            'code: Unique product code (required)',
            'name: Product name (required)',
            'category_name: Product category',
            'unit: Unit of measurement (PCS, BOX, etc)',
            'price: Unit price (numeric)',
            'current_stock: Current stock quantity',
            'minimum_stock: Minimum stock alert level',
            'expired_date: Expiration date (YYYY-MM-DD)',
            'lot_number: Batch/lot number',
            'distribution_permit: Distribution permit number',
            'is_active: TRUE/FALSE (default: TRUE)',
            'migrated_to_product_code: Code of new product if migrated',
            'migration_notes: Reason for migration'
        ];

        $row = 2;
        foreach ($instructions as $instruction) {
            $sheet->setCellValue('O' . $row, $instruction);
            $row++;
        }

        $filename = 'products_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
