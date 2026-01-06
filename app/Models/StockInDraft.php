<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'draft_number',
        'supplier_id',
        'supplier_name',
        'order_number',
        'invoice_number',
        'transaction_date',
        'notes',
        'include_tax',
        'cart_data',
        'total_amount'
    ];

    protected $casts = [
        'cart_data' => 'array',
        'transaction_date' => 'date',
        'include_tax' => 'boolean',
        'total_amount' => 'decimal:2'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function generateDraftNumber()
    {
        $date = now()->format('Ymd');
        $lastDraft = self::whereDate('created_at', now()->toDateString())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastDraft ? (int)substr($lastDraft->draft_number, -3) + 1 : 1;

        return 'DRAFT-IN-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function calculateTotalAmount()
    {
        $subtotal = 0;

        if ($this->cart_data) {
            foreach ($this->cart_data as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];
                $subtotal += $totalPrice;
            }
        }

        $taxAmount = $this->include_tax ? $subtotal * 0.11 : 0;
        return $subtotal + $taxAmount;
    }
}
