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
        'jumlah',
        'spesifikasi',
        'brand',
        'model',
        'serial_number',
        'no_registrasi',
        'tanggal_install',
        'kategori',
        'garansi_berakhir',
        'periode_kso_mulai',
        'periode_kso_berakhir',
        'lokasi_penempatan',
        'kondisi',
        'status'
    ];

    protected $casts = [
        'nilai_item' => 'decimal:2',
        'jumlah' => 'integer',
        'tanggal_install' => 'date',
        'garansi_berakhir' => 'date',
        'periode_kso_mulai' => 'date',
        'periode_kso_berakhir' => 'date'
    ];

    /**
     * Get the KSO item that owns the support item
     */
    public function ksoItem(): BelongsTo
    {
        return $this->belongsTo(KsoItem::class);
    }

    /**
     * Get total value for this support item (nilai_item * jumlah)
     */
    public function getTotalValueAttribute(): float
    {
        return $this->nilai_item * $this->jumlah;
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
        
        return match($status) {
            'Dalam garansi' => 'text-green-600',
            'Garansi akan habis' => 'text-yellow-600',
            'Garansi habis' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    /**
     * Get KSO period status
     */
    public function getKsoPeriodStatusAttribute(): string
    {
        if (!$this->periode_kso_berakhir) {
            return 'Tidak ada periode KSO';
        }

        $now = now();
        $endDate = $this->periode_kso_berakhir;

        if ($endDate->isPast()) {
            return 'KSO berakhir';
        } elseif ($endDate->diffInDays($now) <= 30) {
            return 'KSO akan berakhir';
        } else {
            return 'KSO aktif';
        }
    }

    /**
     * Get KSO period status color class
     */
    public function getKsoPeriodStatusColorAttribute(): string
    {
        $status = $this->kso_period_status;
        
        return match($status) {
            'KSO aktif' => 'text-green-600',
            'KSO akan berakhir' => 'text-yellow-600',
            'KSO berakhir' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    /**
     * Get KSO duration in months
     */
    public function getDurasiKsoBulanAttribute(): ?int
    {
        if (!$this->periode_kso_mulai || !$this->periode_kso_berakhir) {
            return null;
        }

        return $this->periode_kso_mulai->diffInMonths($this->periode_kso_berakhir);
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayAttribute(): string
    {
        return match($this->kategori) {
            'komputer' => 'Komputer',
            'monitor' => 'Monitor',
            'ups' => 'UPS',
            'printer' => 'Printer',
            'keyboard' => 'Keyboard',
            'mouse' => 'Mouse',
            'network' => 'Network',
            'storage' => 'Storage',
            default => ucfirst($this->kategori ?? 'Lainnya')
        };
    }
}
