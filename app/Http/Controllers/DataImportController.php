<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ImportSuppliersImport;
use App\Imports\ImportCustomersImport;
use App\Imports\ImportProductsImport;
use App\Imports\ImportStockMovementsImport;
use App\Imports\ImportKsoItemsImport;
use App\Models\Product;
use App\Exports\KsoItemsTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DataImportController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'super_admin'
        ];
    }
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
            Log::info('Import suppliers request received');
            $import = new ImportSuppliersImport();
            Excel::import($import, $request->file('file'));

            Log::info('Supplier import completed. Rows processed: ' . $import->getRowCount());
            Log::info('Rows skipped: ' . $import->getSkippedRowsCount());
            Log::info('Total rows attempted: ' . $import->getTotalProcessedRows());

            $message = 'Data supplier berhasil diimport! ';
            $message .= $import->getRowCount() . ' baris berhasil, ';
            if ($import->getSkippedRowsCount() > 0) {
                $message .= $import->getSkippedRowsCount() . ' baris dilewati (total: ' . $import->getTotalProcessedRows() . ' baris). ';
                $message .= 'Periksa log untuk detail baris yang gagal.';
            } else {
                $message .= 'dari ' . $import->getTotalProcessedRows() . ' baris total.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $import->getRowCount(),
                'skipped' => $import->getSkippedRowsCount(),
                'total_attempted' => $import->getTotalProcessedRows()
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
            Log::info('Import customers request received');
            $import = new ImportCustomersImport();
            Excel::import($import, $request->file('file'));

            Log::info('Customer import completed. Rows processed: ' . $import->getRowCount());
            Log::info('Rows skipped: ' . $import->getSkippedRowsCount());
            Log::info('Total rows attempted: ' . $import->getTotalProcessedRows());

            $message = 'Data customer berhasil diimport! ';
            $message .= $import->getRowCount() . ' baris berhasil, ';
            if ($import->getSkippedRowsCount() > 0) {
                $message .= $import->getSkippedRowsCount() . ' baris dilewati (total: ' . $import->getTotalProcessedRows() . ' baris). ';
                $message .= 'Periksa log untuk detail baris yang gagal.';
            } else {
                $message .= 'dari ' . $import->getTotalProcessedRows() . ' baris total.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $import->getRowCount(),
                'skipped' => $import->getSkippedRowsCount(),
                'total_attempted' => $import->getTotalProcessedRows()
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
            Log::info('Rows skipped: ' . $import->getSkippedRowsCount());
            Log::info('Total rows attempted: ' . $import->getTotalProcessedRows());

            $message = 'Data produk berhasil diimport! ';
            $message .= $import->getRowCount() . ' baris berhasil, ';
            if ($import->getSkippedRowsCount() > 0) {
                $message .= $import->getSkippedRowsCount() . ' baris dilewati (total: ' . $import->getTotalProcessedRows() . ' baris). ';
                $message .= 'Periksa log untuk detail baris yang gagal.';
            } else {
                $message .= 'dari ' . $import->getTotalProcessedRows() . ' baris total.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $import->getRowCount(),
                'skipped' => $import->getSkippedRowsCount(),
                'total_attempted' => $import->getTotalProcessedRows()
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
     * Import KSO items from Excel
     */
    public function importKsoItems(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            Log::info('Import KSO items request received');
            $import = new ImportKsoItemsImport();
            Excel::import($import, $request->file('file'));

            // Process support items after main items are imported
            $import->afterImport();

            Log::info('KSO items import completed. Rows processed: ' . $import->getRowCount());
            Log::info('Rows skipped: ' . $import->getSkippedRowsCount());
            Log::info('Total rows attempted: ' . $import->getTotalProcessedRows());

            $message = 'Data KSO items berhasil diimport! ';
            $message .= $import->getRowCount() . ' baris berhasil, ';
            if ($import->getSkippedRowsCount() > 0) {
                $message .= $import->getSkippedRowsCount() . ' baris dilewati (total: ' . $import->getTotalProcessedRows() . ' baris). ';
                $message .= 'Periksa log untuk detail baris yang gagal.';
            } else {
                $message .= 'dari ' . $import->getTotalProcessedRows() . ' baris total.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $import->getRowCount(),
                'skipped' => $import->getSkippedRowsCount(),
                'total_attempted' => $import->getTotalProcessedRows()
            ]);
        } catch (\Exception $e) {
            Log::error('KSO items import error: ' . $e->getMessage());
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
            Log::info('Import stock movements request received');
            $import = new ImportStockMovementsImport();
            Excel::import($import, $request->file('file'));

            Log::info('Stock movement import completed. Rows processed: ' . $import->getRowCount());
            Log::info('Rows skipped: ' . $import->getSkippedRowsCount());
            Log::info('Total rows attempted: ' . $import->getTotalProcessedRows());

            $message = 'Data stock movement berhasil diimport! ';
            $message .= $import->getRowCount() . ' baris berhasil, ';
            if ($import->getSkippedRowsCount() > 0) {
                $message .= $import->getSkippedRowsCount() . ' baris dilewati (total: ' . $import->getTotalProcessedRows() . ' baris). ';
                $message .= 'Periksa log untuk detail baris yang gagal.';
            } else {
                $message .= 'dari ' . $import->getTotalProcessedRows() . ' baris total.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'imported' => $import->getRowCount(),
                'skipped' => $import->getSkippedRowsCount(),
                'total_attempted' => $import->getTotalProcessedRows()
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
            'address',
            'is_active',
            'notes'
        ];

        $data = [
            ['PT. Supplier Medika', 'John Doe', 'Jane Smith', '', '021-12345678', '021-87654321', '', 'info@supplier.com', 'Jakarta Selatan', 'TRUE', 'Supplier aktif untuk alat kesehatan'],
            ['PT. Old Supplier Name', 'Old Contact', '', '', '021-9999999', '', '', 'old@supplier.com', 'Jl. Lama', 'FALSE', 'Nama lama - tidak digunakan lagi, hanya untuk data historis']
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
            'address',
            'is_active',
            'notes'
        ];

        $data = [
            ['RS. Sehat', 'Dr. Budi', 'Siti', '', '021-11111111', '021-22222222', '', 'info@rssehat.com', 'Jakarta Pusat', 'TRUE', 'Customer aktif untuk rumah sakit'],
            ['RS. Lama', 'Dr. Old', '', '', '021-9999999', '', '', 'old@rs.com', 'Jakarta Lama', 'FALSE', 'Nama lama - tidak digunakan lagi, hanya untuk data historis']
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
     * Download template for KSO items
     */
    public function downloadKsoItemTemplate()
    {
        $data = [
            // Main KSO Item Example
            [
                'item_type' => 'main',
                'main_item_no_registrasi' => '',
                'customer_name' => 'RS. Sehat',
                'nama_alat' => 'Hematology Analyzer Mindray BC-6800',
                'brand' => 'Mindray',
                'model' => 'BC-6800',
                'serial_number' => 'SN001234567',
                'no_registrasi' => 'REG001',
                'kategori' => 'hematologi',
                'nilai_alat_utama' => '450000000',
                'butuh_komputer' => 'TRUE',
                'keterangan' => 'Alat hematologi lengkap dengan reagen',
                'spesifikasi_teknis' => '120 parameter, throughput 80 samples/jam',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab Utama Lantai 2',
                'pic_customer' => 'Dr. Budi',
                'pic_msa' => 'Technician MSA',
                'tanggal_investasi' => '2024-01-15',
                'tanggal_install' => '2024-01-20',
                'tanggal_deployment' => '2024-01-20',
                'garansi_mulai' => '2024-01-20',
                'garansi_berakhir' => '2027-01-19',
                'periode_kso_mulai' => '2024-01-20',
                'periode_kso_berakhir' => '2027-01-20',
                'durasi_kso_bulan' => '36',
                'status' => 'active'
            ],
            // Support Item Example
            [
                'item_type' => 'support',
                'main_item_no_registrasi' => 'REG001',
                'customer_name' => '',
                'nama_alat' => 'PC Support Hematology',
                'brand' => 'Dell',
                'model' => 'OptiPlex 7090',
                'serial_number' => 'PC001234',
                'no_registrasi' => '',
                'kategori' => 'komputer',
                'nilai_alat_utama' => '15000000',
                'butuh_komputer' => '',
                'keterangan' => 'PC untuk software hematologi',
                'spesifikasi_teknis' => 'Intel i7, 16GB RAM, 512GB SSD',
                'kondisi' => 'baik',
                'lokasi_penempatan' => 'Lab Utama Lantai 2',
                'pic_customer' => '',
                'pic_msa' => '',
                'tanggal_investasi' => '',
                'tanggal_install' => '2024-01-20',
                'tanggal_deployment' => '',
                'garansi_mulai' => '',
                'garansi_berakhir' => '2027-01-19',
                'periode_kso_mulai' => '',
                'periode_kso_berakhir' => '',
                'durasi_kso_bulan' => '',
                'status' => 'active'
            ]
        ];

        return Excel::download(new KsoItemsTemplateExport($data), 'kso_items_template.xlsx');
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

        // Check if first row of data is headers (for KSO items template)
        if (is_array($data) && count($data) > 0 && is_array($data[0]) && $data[0] === $headers) {
            // Data already includes headers, use as-is
            $sheet->fromArray($data, null, 'A1');
        } else {
            // Add headers first, then data
            $sheet->fromArray($headers, null, 'A1');
            $sheet->fromArray($data, null, 'A2');
        }

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
     * Format boolean value for preview display
     */
    private function formatBooleanPreview($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_numeric($value)) {
            return (int)$value === 1 ? 'true' : 'false';
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));
            if (in_array($value, ['false', '0', 'no', 'tidak', 'inactive', 'tidak aktif'])) {
                return 'false';
            }
            if (in_array($value, ['true', '1', 'yes', 'ya', 'active', 'aktif'])) {
                return 'true';
            }
        }

        return 'true'; // Default
    }

    /**
     * Preview Excel data before import
     */
    public function previewData(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'type' => 'required|in:suppliers,customers,products,stock_movements,kso_items'
        ]);

        try {
            $data = Excel::toArray([], $request->file('file'));
            $rows = $data[0] ?? [];

            // Get first 10 rows for preview
            $preview = array_slice($rows, 0, 10);
            $totalRows = count($rows);

            // Process preview data to show boolean values properly
            if ($request->type === 'products' || $request->type === 'kso_items') {
                $preview = array_map(function ($row) {
                    if (isset($row['is_active'])) {
                        $row['is_active'] = $this->formatBooleanPreview($row['is_active']);
                    }
                    if (isset($row['butuh_komputer'])) {
                        $row['butuh_komputer'] = $this->formatBooleanPreview($row['butuh_komputer']);
                    }
                    return $row;
                }, $preview);
            }

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
