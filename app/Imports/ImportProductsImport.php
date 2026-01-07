<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductCategory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ImportProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, SkipsEmptyRows, WithBatchInserts, WithChunkReading
{
    private $rowCount = 0;
    private $productCache = [];

    /**
     * Find product by code with caching
     */
    private function findProductByCode($code)
    {
        if (empty($code)) {
            return null;
        }

        if (!isset($this->productCache[$code])) {
            $this->productCache[$code] = Product::where('code', $code)->first();
        }

        return $this->productCache[$code];
    }

    /**
     * @param array $row
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Find category by name or create default one
            $categoryName = !empty($row['category_name']) ? $row['category_name'] : 'Uncategorized';
            $category = ProductCategory::firstOrCreate(
                ['name' => $categoryName],
                ['name' => $categoryName]
            );

            $this->rowCount++;

            // Handle expired_date format from Excel
            $expiredDate = null;
            if (!empty($row['expired_date'])) {
                if (is_numeric($row['expired_date'])) {
                    // Excel date format (numeric)
                    $expiredDate = \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime('1899-12-30') . ' + ' . (int)$row['expired_date'] . ' days'))->format('Y-m-d');
                } else {
                    // Try to parse as string date
                    try {
                        $expiredDate = \Carbon\Carbon::parse($row['expired_date'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $expiredDate = null;
                    }
                }
            }

            return new Product([
                'code' => $row['code'],
                'name' => $row['name'],
                'description' => $row['description'] ?? '',
                'category_id' => $category->id,
                'unit' => !empty($row['unit']) ? $row['unit'] : 'PCS',
                'lot_number' => $row['lot_number'] ?? '',
                'expired_date' => $expiredDate,
                'distribution_permit' => $row['distribution_permit'] ?? '',
                'price' => is_numeric($row['price']) && $row['price'] !== '' ? $row['price'] : 0,
                'current_stock' => is_numeric($row['current_stock']) && $row['current_stock'] !== '' ? $row['current_stock'] : 0,
                'minimum_stock' => is_numeric($row['minimum_stock']) ? $row['minimum_stock'] : 0,
                'is_active' => isset($row['is_active']) ? $this->parseBoolean($row['is_active']) : true,
                'migrated_to_product_id' => !empty($row['migrated_to_product_code']) ? $this->findProductByCode($row['migrated_to_product_code'])?->id : null,
                'migration_notes' => $row['migration_notes'] ?? '',
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing row: ' . json_encode($row));
            Log::error('Error message: ' . $e->getMessage());
            return null; // Skip this row
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'category_name' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'current_stock' => 'nullable|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'expired_date' => 'nullable|string|max:255',
            'lot_number' => 'nullable|string|max:255',
            'distribution_permit' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|string|in:true,false,1,0,TRUE,FALSE,True,False',
            'migrated_to_product_code' => 'nullable|string|max:255',
            'migration_notes' => 'nullable|string',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'code.required' => 'Kode produk wajib diisi',
            'code.unique' => 'Kode produk sudah ada',
            'name.required' => 'Nama produk wajib diisi',
            'category_name.required' => 'Nama kategori wajib diisi',
            'unit.required' => 'Satuan wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',
            'expired_date.date' => 'Format tanggal kadaluarsa tidak valid',
        ];
    }

    /**
     * Batch size for insert
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Handle errors
     */
    public function onError(\Throwable $e)
    {
        Log::error('ImportProductsImport Error: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
    }

    /**
     * Handle validation failures
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::error('ImportProductsImport Validation Failure:');
            Log::error('Row: ' . $failure->row());
            Log::error('Attribute: ' . $failure->attribute());
            Log::error('Errors: ' . implode(', ', $failure->errors()));
            Log::error('Values: ' . json_encode($failure->values()));
        }
    }

    /**
     * Parse boolean value from Excel with flexible format handling
     */
    private function parseBoolean($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int)$value === 1;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));
            // Check for false values first
            if (in_array($value, ['false', '0', 'no', 'tidak', 'inactive', 'tidak aktif'])) {
                return false;
            }
            // Then check for true values
            return in_array($value, ['true', '1', 'yes', 'ya', 'active', 'aktif']);
        }

        return true; // Default to true if value is null/empty
    }

    /**
     * Get row count
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
