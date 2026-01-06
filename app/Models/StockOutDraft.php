<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'draft_number',
        'customer_id',
        'customer_name',
        'order_number',
        'invoice_number',
        'transaction_date',
        'notes',
        'payment_terms',
        'delivery_number',
        'bank_option',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function generateDraftNumber()
    {
        $date = now()->format('Ymd');
        $lastDraft = self::whereDate('created_at', now()->toDateString())
                        ->orderBy('id', 'desc')
                        ->first();
        
        $sequence = $lastDraft ? (int)substr($lastDraft->draft_number, -3) + 1 : 1;
        
        return 'DRAFT-' . $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function calculateTotalAmount()
    {
        $subtotal = 0;
        
        if ($this->cart_data) {
            foreach ($this->cart_data as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];
                $discountPercent = $item['discount'] ?? 0;
                $discountAmount = $totalPrice * ($discountPercent / 100);
                $nettoAmount = $totalPrice - $discountAmount;
                $subtotal += $nettoAmount;
            }
        }
        
        $taxAmount = $this->include_tax ? $subtotal * 0.11 : 0;
        return $subtotal + $taxAmount;
    }
}
