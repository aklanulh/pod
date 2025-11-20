<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KsoItem extends Model
{
    use HasFactory;

    /**
     * Boot method to auto-generate unique_id
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->unique_id)) {
                $model->unique_id = self::generateUniqueId();
            }
        });
    }

    protected $fillable = [
        'customer_id',
        'unique_id',
        'nama_alat',
        'brand',
        'model',
        'serial_number',
        'no_registrasi',
        'kategori',
        'nilai_alat_utama',
        'nilai_sewa_bulanan',
        'deposit',
        'butuh_komputer',
        'total_pendukung',
        'total_investasi',
        'keterangan',
        'spesifikasi_teknis',
        'kondisi',
        'lokasi_penempatan',
        'pic_customer',
        'pic_msa',
        'tanggal_investasi',
        'tanggal_install',
        'garansi_mulai',
        'garansi_berakhir',
        'periode_kso_mulai',
        'periode_kso_berakhir',
        'durasi_kso_bulan',
        'status'
    ];

    protected $casts = [
        'nilai_alat_utama' => 'decimal:2',
        'nilai_sewa_bulanan' => 'decimal:2',
        'deposit' => 'decimal:2',
        'total_pendukung' => 'decimal:2',
        'total_investasi' => 'decimal:2',
        'butuh_komputer' => 'boolean',
        'tanggal_investasi' => 'date',
        'tanggal_install' => 'date',
        'garansi_mulai' => 'date',
        'garansi_berakhir' => 'date',
        'periode_kso_mulai' => 'date',
        'periode_kso_berakhir' => 'date'
    ];

    /**
     * Get the customer that owns the KSO item
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the support items for the KSO item
     */
    public function supportItems(): HasMany
    {
        return $this->hasMany(KsoSupportItem::class);
    }

    /**
     * Calculate total support items value
     */
    public function calculateTotalPendukung(): float
    {
        return $this->supportItems->sum(function ($item) {
            return $item->nilai_item * $item->jumlah;
        });
    }

    /**
     * Calculate total investment (main equipment + support items)
     */
    public function calculateTotalInvestasi(): float
    {
        return $this->nilai_alat_utama + $this->calculateTotalPendukung();
    }

    /**
     * Update calculated totals
     */
    public function updateCalculatedTotals(): void
    {
        $this->total_pendukung = $this->calculateTotalPendukung();
        $this->total_investasi = $this->calculateTotalInvestasi();
        $this->save();
    }

    /**
     * Get customer's total sales from stock movements (same calculation as customer report)
     */
    public function getCustomerTotalSales(): float
    {
        $stats = $this->customer->stockMovements()
            ->where('type', 'out')
            ->where('transaction_date', '>=', $this->tanggal_investasi)
            ->selectRaw('SUM(quantity * COALESCE(unit_price, 0)) as total_value')
            ->first();

        return $stats->total_value ?? 0;
    }

    /**
     * Calculate ROI percentage for this specific item
     */
    public function calculateItemROI(): float
    {
        $totalSales = $this->getCustomerTotalSales();

        if ($this->total_investasi <= 0) {
            return 0;
        }

        return ($totalSales / $this->total_investasi) * 100;
    }

    /**
     * Check if this item has achieved ROI
     */
    public function hasAchievedROI(): bool
    {
        return $this->calculateItemROI() >= 100;
    }

    /**
     * Get ROI status text
     */
    public function getROIStatusAttribute(): string
    {
        return $this->hasAchievedROI() ? 'ROI' : 'Belum ROI';
    }

    /**
     * Get ROI status color class
     */
    public function getROIStatusColorAttribute(): string
    {
        return $this->hasAchievedROI() ? 'text-green-600' : 'text-red-600';
    }

    /**
     * Calculate ROI difference for this item (profit or loss from 100% target)
     */
    public function calculateItemROIDifference(): array
    {
        $totalSales = $this->getCustomerTotalSales();
        $roiPercentage = $this->calculateItemROI();

        // Target is 100% ROI (sales = investment)
        $targetSales = $this->total_investasi;
        $difference = $totalSales - $targetSales;

        return [
            'amount' => abs($difference),
            'type' => $difference >= 0 ? 'profit' : 'kurang',
            'percentage_diff' => $roiPercentage - 100
        ];
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for items by customer
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope for items by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('kategori', $category);
    }

    /**
     * Get warranty status
     */
    public function getWarrantyStatusAttribute(): string
    {
        if (!$this->garansi_berakhir) {
            return 'Tidak ada garansi';
        }

        $today = now();
        if ($this->garansi_berakhir < $today) {
            return 'Garansi habis';
        } elseif ($this->garansi_berakhir->diffInDays($today) <= 30) {
            return 'Garansi akan habis';
        } else {
            return 'Dalam garansi';
        }
    }

    /**
     * Get warranty status color
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

    /**
     * Get KSO period status
     */
    public function getKsoPeriodStatusAttribute(): string
    {
        if (!$this->periode_kso_berakhir) {
            return 'Periode tidak ditentukan';
        }

        $today = now();
        if ($this->periode_kso_berakhir < $today) {
            return 'KSO berakhir';
        } elseif ($this->periode_kso_berakhir->diffInDays($today) <= 30) {
            return 'KSO akan berakhir';
        } else {
            return 'KSO aktif';
        }
    }

    /**
     * Get KSO period status color
     */
    public function getKsoPeriodStatusColorAttribute(): string
    {
        $status = $this->kso_period_status;
        return match ($status) {
            'KSO aktif' => 'text-green-600',
            'KSO akan berakhir' => 'text-yellow-600',
            'KSO berakhir' => 'text-red-600',
            default => 'text-gray-600'
        };
    }

    /**
     * Get equipment condition color
     */
    public function getConditionColorAttribute(): string
    {
        return match ($this->kondisi) {
            'baik' => 'text-green-600',
            'rusak ringan' => 'text-yellow-600',
            'rusak berat' => 'text-red-600',
            'maintenance' => 'text-blue-600',
            default => 'text-gray-600'
        };
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayAttribute(): string
    {
        return match ($this->kategori) {
            'hematologi' => 'Hematologi',
            'kimia_klinik' => 'Kimia Klinik',
            'gas_darah' => 'Gas Darah',
            'koagulasi' => 'Koagulasi',
            'mikrobiologi' => 'Mikrobiologi',
            'preparasi_sampel' => 'Preparasi Sampel',
            'imaging' => 'Imaging',
            'monitoring' => 'Monitoring',
            'lainnya' => 'Lainnya',
            default => 'Tidak dikategorikan'
        };
    }

    /**
     * Calculate remaining warranty days
     */
    public function getRemainingWarrantyDaysAttribute(): ?int
    {
        if (!$this->garansi_berakhir) {
            return null;
        }

        $today = now();
        if ($this->garansi_berakhir < $today) {
            return 0;
        }

        return $today->diffInDays($this->garansi_berakhir);
    }

    /**
     * Calculate remaining KSO days
     */
    public function getRemainingKsoDaysAttribute(): ?int
    {
        if (!$this->periode_kso_berakhir) {
            return null;
        }

        $today = now();
        if ($this->periode_kso_berakhir < $today) {
            return 0;
        }

        return $today->diffInDays($this->periode_kso_berakhir);
    }

    /**
     * Generate unique 8-digit ID
     * Format: YYYYMMSSSS
     * - YY: Tahun (2 digit, contoh: 25 untuk 2025)
     * - MM: Bulan (2 digit, 01-12)
     * - SSSS: Urutan barang dalam setahun (4 digit, 0001-9999)
     */
    public static function generateUniqueId(): string
    {
        $now = now();
        $year = $now->format('y');      // 2 digit tahun (25 untuk 2025)
        $month = $now->format('m');     // 2 digit bulan (01-12)

        // Hitung urutan barang dalam setahun
        // Cari barang dengan tahun yang sama, ambil urutan tertinggi
        $lastSequence = self::whereRaw("substr(unique_id, 1, 2) = ?", [$year])
            ->orderByRaw("CAST(substr(unique_id, 5, 4) AS INTEGER) DESC")
            ->first();

        if ($lastSequence) {
            // Ambil 4 digit terakhir dan tambah 1
            $sequence = (int) substr($lastSequence->unique_id, 4) + 1;
        } else {
            // Jika tidak ada barang di tahun ini, mulai dari 1
            $sequence = 1;
        }

        // Format: YYMMSSSS (8 digit)
        $uniqueId = $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return $uniqueId;
    }

    /**
     * Find KSO item by unique_id
     */
    public static function findByUniqueId($uniqueId)
    {
        return self::where('unique_id', $uniqueId)->first();
    }

    /**
     * Scope to find by unique_id
     */
    public function scopeByUniqueId($query, $uniqueId)
    {
        return $query->where('unique_id', $uniqueId);
    }
}
