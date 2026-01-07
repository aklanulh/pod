<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ImportSuppliersImport;
use App\Imports\ImportCustomersImport;
use App\Imports\ImportProductsImport;
use App\Imports\ImportStockMovementsImport;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DataImportController extends Controller
{
    /**
     * Display import page
     */
    public function index()
    {
        return view('data-import.index');
    }

    /**
     * Import suppliers from Excel
     */
    public function importSuppliers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $import = new ImportSuppliersImport();
            Excel::import($import, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Data supplier berhasil diimport!',
                'imported' => method_exists($import, 'getRowCount') ? $import->getRowCount() : 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import customers from Excel
     */
    public function importCustomers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $import = new ImportCustomersImport();
            Excel::import($import, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => 'Data customer berhasil diimport!',
                'imported' => method_exists($import, 'getRowCount') ? $import->getRowCount() : 0
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import products from Excel
     */
    public function importProducts(Request $request)
    {
        try {
            Log::info('Import products request received');
            Log::info('Request method: ' . $request->method());
            Log::info('Request data: ' . json_encode($request->all()));

            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv|max:10240'
            ]);

            Log::info('Validation passed, starting import');
            $import = new ImportProductsImport();

            // Don't clear existing products for now - just add new ones
            // Product::query()->delete();

            Excel::import($import, $request->file('file'));

            Log::info('Import completed successfully');
            Log::info('Rows processed: ' . $import->getRowCount());

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diimport! ' . $import->getRowCount() . ' baris diproses.',
                'imported' => $import->getRowCount()
            ]);
        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Import stock movements from Excel
     */
    public function importStockMovements(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            Log::info('Starting stock movement import');
            $import = new ImportStockMovementsImport();
            Excel::import($import, $request->file('file'));

            $rowCount = method_exists($import, 'getRowCount') ? $import->getRowCount() : 0;
            Log::info('Stock movement import completed. Rows processed: ' . $rowCount);

            return response()->json([
                'success' => true,
                'message' => 'Data stock movement berhasil diimport! ' . $rowCount . ' baris diproses.',
                'imported' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('Stock movement import error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download template for suppliers
     */
    public function downloadSupplierTemplate()
    {
        $headers = [
            'name',
            'contact_person',
            'contact_person_2',
            'contact_person_3',
            'phone',
            'phone_2',
            'phone_3',
            'email',
            'address'
        ];

        $data = [
            ['PT. Supplier Medika', 'John Doe', 'Jane Smith', '', '021-12345678', '021-87654321', '', 'info@supplier.com', 'Jakarta Selatan']
        ];

        return $this->createExcelFile('supplier_template.xlsx', $headers, $data);
    }

    /**
     * Download template for customers
     */
    public function downloadCustomerTemplate()
    {
        $headers = [
            'name',
            'contact_person',
            'contact_person_2',
            'contact_person_3',
            'phone',
            'phone_2',
            'phone_3',
            'email',
            'address'
        ];

        $data = [
            ['RS. Sehat', 'Dr. Budi', 'Siti', '', '021-11111111', '021-22222222', '', 'info@rssehat.com', 'Jakarta Pusat']
        ];

        return $this->createExcelFile('customer_template.xlsx', $headers, $data);
    }

    /**
     * Download template for products
     */
    public function downloadProductTemplate()
    {
        $headers = [
            'code',
            'name',
            'description',
            'category_name',
            'unit',
            'lot_number',
            'expired_date',
            'distribution_permit',
            'price',
            'current_stock',
            'minimum_stock',
            'is_active',
            'migrated_to_product_code',
            'migration_notes'
        ];

        $data = [
            ['PRD-001', 'Alat Tes Darah', 'Alat tes darah lengkap', 'Alat Kesehatan', 'Box', 'LOT001', '2024-12-31', 'DIST001', 150000, 100, 10, 'TRUE', '', '']
        ];

        return $this->createExcelFile('product_template.xlsx', $headers, $data);
    }

    /**
     * Download template for stock movements
     */
    public function downloadStockMovementTemplate()
    {
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

        $data = [
            ['SM-20231201-001', 'ORD-001', 'INV-001', 'PRD-001', 'in', 10, 150000, 'TRUE', 0, 0, 'NET 30', 'PT. Supplier Medika', '', 'Pembelian alat', '2023-12-01 10:00:00'],
            ['SM-20231201-002', 'ORD-002', 'INV-002', 'PRD-001', 'out', 5, 200000, 'TRUE', 0, 0, 'NET 30', '', 'RS. Sehat', 'Penjualan alat', '2023-12-01 14:00:00']
        ];

        return $this->createExcelFile('stock_movement_template.xlsx', $headers, $data);
    }

    /**
     * Create Excel file with headers and data
     */
    private function createExcelFile($filename, $headers, $data)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $sheet->fromArray($headers, null, 'A1');

        // Add data
        $sheet->fromArray($data, null, 'A2');

        // Style header row
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE0E0E0');

        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Preview Excel data before import
     */
    public function previewData(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'type' => 'required|in:suppliers,customers,products,stock_movements'
        ]);

        try {
            $data = Excel::toArray([], $request->file('file'));
            $rows = $data[0] ?? [];

            // Get first 10 rows for preview
            $preview = array_slice($rows, 0, 10);
            $totalRows = count($rows);

            return response()->json([
                'success' => true,
                'preview' => $preview,
                'total_rows' => $totalRows,
                'headers' => !empty($preview) ? array_keys($preview[0]) : []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
