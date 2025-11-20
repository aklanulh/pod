<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'order_number',
        'invoice_number',
        'product_id',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'include_tax',
        'tax_amount',
        'subtotal_amount',
        'final_amount',
        'supplier_id',
        'customer_id',
        'notes',
        'payment_terms',
        'transaction_date'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'include_tax' => 'boolean',
        'transaction_date' => 'datetime'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeStockIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeStockOut($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeOpname($query)
    {
        return $query->where('type', 'opname');
    }

    /**
     * Get optimized statistics for customer transactions
     * 
     * @param int $customerId
     * @return object
     */
    public static function getCustomerStats($customerId)
    {
        return self::where('customer_id', $customerId)
            ->where('type', 'out')
            ->selectRaw('
                COUNT(*) as total_transactions,
                SUM(quantity) as total_quantity,
                SUM(quantity * COALESCE(unit_price, 0)) as total_value
            ')
            ->first();
    }

    /**
     * Get optimized statistics for supplier transactions
     * 
     * @param int $supplierId
     * @return object
     */
    public static function getSupplierStats($supplierId)
    {
        return self::where('supplier_id', $supplierId)
            ->where('type', 'in')
            ->selectRaw('
                COUNT(*) as total_transactions,
                SUM(quantity) as total_quantity,
                SUM(quantity * COALESCE(unit_price, 0)) as total_value
            ')
            ->first();
    }

    /**
     * Get optimized chart data for customer purchases by month
     * 
     * @param int $customerId
     * @param int $year
     * @return \Illuminate\Support\Collection
     */
    public static function getCustomerChartData($customerId, $year)
    {
        $monthExpr = config('database.default') === 'sqlite'
            ? "CAST(strftime('%m', stock_movements.transaction_date) AS INTEGER)"
            : "MONTH(stock_movements.transaction_date)";

        return self::join('products', 'stock_movements.product_id', '=', 'products.id')
            ->where('stock_movements.customer_id', $customerId)
            ->where('stock_movements.type', 'out')
            ->whereYear('stock_movements.transaction_date', $year)
            ->selectRaw("
                products.id as product_id,
                products.name as product_name,
                $monthExpr as month,
                SUM(stock_movements.quantity) as total_quantity
            ")
            ->groupBy('products.id', 'products.name', 'month')
            ->orderBy('products.name')
            ->get()
            ->groupBy('product_id');
    }

    /**
     * Get optimized chart data for product sales by customer and month
     * 
     * @param int $productId
     * @param int $year
     * @return \Illuminate\Support\Collection
     */
    public static function getProductChartData($productId, $year)
    {
        $monthExpr = config('database.default') === 'sqlite'
            ? "CAST(strftime('%m', stock_movements.transaction_date) AS INTEGER)"
            : "MONTH(stock_movements.transaction_date)";

        return self::join('customers', 'stock_movements.customer_id', '=', 'customers.id')
            ->where('stock_movements.product_id', $productId)
            ->where('stock_movements.type', 'out')
            ->whereYear('stock_movements.transaction_date', $year)
            ->selectRaw("
                customers.id as customer_id,
                customers.name as customer_name,
                $monthExpr as month,
                SUM(stock_movements.quantity) as total_quantity
            ")
            ->groupBy('customers.id', 'customers.name', 'month')
            ->orderBy('customers.name')
            ->get()
            ->groupBy('customer_id');
    }
}
