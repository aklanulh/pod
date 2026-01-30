<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMerge extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_customer_id',
        'target_customer_id',
        'reason',
        'merged_by',
    ];

    public function sourceCustomer()
    {
        return $this->belongsTo(Customer::class, 'source_customer_id');
    }

    public function targetCustomer()
    {
        return $this->belongsTo(Customer::class, 'target_customer_id');
    }

    public function mergedBy()
    {
        return $this->belongsTo(User::class, 'merged_by');
    }
}
