<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QcRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'kso_item_id',
        'type', // 'qc' or 'calibration'
        'date',
        'status', // 'pass', 'fail', 'pending'
        'technician_name',
        'notes',
        'certificate_file',
        'next_due_date',
        'parameters',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'next_due_date' => 'date',
        'parameters' => 'array',
    ];

    public function ksoItem(): BelongsTo
    {
        return $this->belongsTo(KsoItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pass' => 'bg-green-100 text-green-800',
            'fail' => 'bg-red-100 text-red-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pass' => '✅ Lulus',
            'fail' => '❌ Gagal',
            'pending' => '⏳ Pending',
            default => 'Unknown'
        };
    }

    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'qc' => 'QC',
            'calibration' => 'Kalibrasi',
            default => 'Unknown'
        };
    }
}
