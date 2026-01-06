<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'unit',
        'lot_number',
        'expired_date',
        'distribution_permit',
        'price',
        'current_stock',
        'minimum_stock',
        'is_active',
        'migrated_to_product_id',
        'migration_notes'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'expired_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockOpnameDetails()
    {
        return $this->hasMany(StockOpnameDetail::class);
    }

    public function customerSchedules()
    {
        return $this->hasMany(CustomerSchedule::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    public function isLowStock()
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function isExpired()
    {
        return $this->expired_date && $this->expired_date->isPast();
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->expired_date && $this->expired_date->diffInDays(now()) <= $days && !$this->isExpired();
    }

    public function migratedToProduct()
    {
        return $this->belongsTo(Product::class, 'migrated_to_product_id');
    }

    public function migratedFromProducts()
    {
        return $this->hasMany(Product::class, 'migrated_to_product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function canBeDeleted()
    {
        return $this->is_active && $this->stockMovements()->count() === 0;
    }
}
