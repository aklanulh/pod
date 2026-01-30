<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierMerge extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_supplier_id',
        'target_supplier_id',
        'reason',
        'merged_by',
    ];

    protected $casts = [
        'merged_at' => 'datetime',
    ];

    public function sourceSupplier()
    {
        return $this->belongsTo(Supplier::class, 'source_supplier_id');
    }

    public function targetSupplier()
    {
        return $this->belongsTo(Supplier::class, 'target_supplier_id');
    }

    public function mergedBy()
    {
        return $this->belongsTo(User::class, 'merged_by');
    }
}
