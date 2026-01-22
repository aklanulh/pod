<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KsoSupportItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'kso_item_id',
        'nama_item',
        'nilai_item',
        'spesifikasi',
        'brand',
        'model',
        'serial_number',
        'no_registrasi',
        'garansi_berakhir',
        'kondisi',
        'status'
    ];

    protected $casts = [
        'nilai_item' => 'decimal:2',
        'garansi_berakhir' => 'date'
    ];

    /**
     * Get the KSO item that owns the support item
     */
    public function ksoItem(): BelongsTo
    {
        return $this->belongsTo(KsoItem::class);
    }

    /**
     * Get total value for this support item (nilai_item)
     */
    public function getTotalValueAttribute(): float
    {
        return $this->nilai_item;
    }

    /**
     * Get formatted total value
     */
    public function getFormattedTotalValueAttribute(): string
    {
        return 'Rp ' . number_format($this->total_value, 0, ',', '.');
    }

    /**
     * Get formatted unit value
     */
    public function getFormattedNilaiItemAttribute(): string
    {
        return 'Rp ' . number_format($this->nilai_item, 0, ',', '.');
    }

    /**
     * Get warranty status
     */
    public function getWarrantyStatusAttribute(): string
    {
        if (!$this->garansi_berakhir) {
            return 'Tidak ada garansi';
        }

        $now = now();
        $warranty = $this->garansi_berakhir;

        if ($warranty->isPast()) {
            return 'Garansi habis';
        } elseif ($warranty->diffInDays($now) <= 30) {
            return 'Garansi akan habis';
        } else {
            return 'Dalam garansi';
        }
    }

    /**
     * Get warranty status color class
     */
    public function getWarrantyStatusColorAttribute(): string
    {
        $status = $this->warranty_status;

        return match ($status) {
            'Dalam garansi' => 'text-green-600',
            'Garansi akan habis' => 'text-yellow-600',
            'Garansi habis' => 'text-red-600',
            default => 'text-gray-600'
        };
    }
}
