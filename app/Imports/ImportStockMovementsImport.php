<?php

namespace App\Imports;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ImportStockMovementsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, SkipsEmptyRows
{
    private $rowCount = 0;
    /**
     * @param array $row
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowCount++;
        Log::info('Processing stock movement row: ' . json_encode($row));

        // Find product by code
        $product = Product::where('code', $row['product_code'])->first();
        if (!$product) {
            Log::warning('Product not found with code: ' . $row['product_code']);
            return null; // Skip if product not found
        }

        Log::info('Product found: ' . $product->name . ' (ID: ' . $product->id . ')');

        // Find supplier by name (for 'in' type)
        $supplier = null;
        if ($row['type'] === 'in' && !empty($row['supplier_name'])) {
            $supplier = Supplier::where('name', $row['supplier_name'])->first();
        }

        // Find customer by name (for 'out' type)
        $customer = null;
        if ($row['type'] === 'out' && !empty($row['customer_name'])) {
            $customer = Customer::where('name', $row['customer_name'])->first();
        }

        // Generate unique reference number if not provided
        $referenceNumber = $row['reference_number'] ?? 'SM-' . date('Ymd') . '-' . Str::upper(Str::random(6));

        // Calculate stock values
        $stockBefore = $product->current_stock;
        $quantity = (int) $row['quantity'];
        $stockAfter = $row['type'] === 'in' ? $stockBefore + $quantity : $stockBefore - $quantity;

        // Calculate financial values
        $unitPrice = $row['unit_price'] ?? 0;
        $includeTax = $row['include_tax'] ?? false;

        // Handle include_tax conversion from Excel
        if (is_string($includeTax)) {
            $includeTax = strtolower($includeTax) === 'true' || strtolower($includeTax) === 'yes' || $includeTax === '1';
        }

        $taxAmount = $includeTax ? ($unitPrice * $quantity * 0.11) : 0; // 11% tax
        $subtotalAmount = $unitPrice * $quantity;
        $finalAmount = $subtotalAmount + $taxAmount;

        $stockMovement = new StockMovement([
            'reference_number' => $referenceNumber,
            'order_number' => isset($row['order_number']) ? (string) $row['order_number'] : null,
            'invoice_number' => isset($row['invoice_number']) ? (string) $row['invoice_number'] : null,
            'product_id' => $product->id,
            'type' => $row['type'],
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'unit_price' => $unitPrice,
            'include_tax' => $includeTax,
            'tax_amount' => $taxAmount,
            'subtotal_amount' => $subtotalAmount,
            'final_amount' => $finalAmount,
            'discount_percent' => $row['discount_percent'] ?? 0,
            'discount_amount' => $row['discount_amount'] ?? 0,
            'payment_terms' => $row['payment_terms'] ?? '',
            'supplier_id' => $supplier?->id,
            'customer_id' => $customer?->id,
            'notes' => $row['notes'] ?? null,
            'transaction_date' => !empty($row['transaction_date']) ? $this->parseTransactionDate($row['transaction_date'])->format('Y-m-d H:i:s') : now(),
        ]);

        Log::info('StockMovement model created successfully');
        return $stockMovement;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'product_code' => 'required|string|exists:products,code',
            'type' => 'required|in:in,out,opname',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'transaction_date' => 'nullable|string',
            'reference_number' => 'nullable|string|max:255|unique:stock_movements,reference_number',
            'order_number' => 'nullable|max:255',
            'invoice_number' => 'nullable|max:255',
            'supplier_name' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'include_tax' => 'nullable', // Remove strict validation, handle in model method
            'discount_percent' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'product_code.required' => 'Kode produk wajib diisi',
            'product_code.exists' => 'Kode produk tidak ditemukan',
            'type.required' => 'Tipe transaksi wajib diisi',
            'type.in' => 'Tipe transaksi harus: in, out, atau opname',
            'quantity.required' => 'Quantity wajib diisi',
            'quantity.min' => 'Quantity harus lebih dari 0',
            'unit_price.numeric' => 'Harga satuan harus berupa angka',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid',
        ];
    }

    /**
     * Handle errors
     */
    public function onError(\Throwable $e)
    {
        Log::error('Stock movement import error: ' . $e->getMessage());
        // Log error or handle silently
    }

    /**
     * Handle validation failures
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::error('Stock movement validation failure: ' . implode(', ', $failure->errors()));
            Log::error('Failed row data: ' . json_encode($failure->row()));
        }
        // Handle validation failures
    }

    /**
     * Parse transaction date with flexible format handling
     */
    private function parseTransactionDate($date)
    {
        try {
            return \Carbon\Carbon::parse($date);
        } catch (\Exception $e) {
            Log::warning('Invalid date format, using current time: ' . $date);
            return now();
        }
    }

    /**
     * Get row count
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
