<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'opname_number',
        'opname_date',
        'notes',
        'status'
    ];

    protected $casts = [
        'opname_date' => 'date'
    ];

    public function details()
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
