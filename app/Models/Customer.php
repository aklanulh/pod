<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'contact_person_2',
        'contact_person_3',
        'phone',
        'phone_2',
        'phone_3',
        'email',
        'address'
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function customerSchedules()
    {
        return $this->hasMany(CustomerSchedule::class);
    }

    public function ksoItems()
    {
        return $this->hasMany(KsoItem::class);
    }

    /**
     * Get total KSO investment for this customer
     */
    public function getTotalKsoInvestment(): float
    {
        return $this->ksoItems()->where('status', 'active')->sum('total_investasi');
    }

    /**
     * Get total sales value for this customer (same calculation as customer report)
     */
    public function getTotalSalesValue(): float
    {
        $stats = $this->stockMovements()
            ->where('type', 'out')
            ->selectRaw('SUM(quantity * COALESCE(unit_price, 0)) as total_value')
            ->first();
            
        return $stats->total_value ?? 0;
    }

    /**
     * Calculate overall ROI for this customer
     */
    public function calculateOverallROI(): float
    {
        $totalInvestment = $this->getTotalKsoInvestment();
        $totalSales = $this->getTotalSalesValue();
        
        if ($totalInvestment <= 0) {
            return 0;
        }
        
        return ($totalSales / $totalInvestment) * 100;
    }

    /**
     * Check if customer has achieved overall ROI
     */
    public function hasAchievedOverallROI(): bool
    {
        return $this->calculateOverallROI() >= 100;
    }

    /**
     * Calculate ROI difference (profit or loss from 100% target)
     */
    public function calculateROIDifference(): array
    {
        $totalInvestment = $this->getTotalKsoInvestment();
        $totalSales = $this->getTotalSalesValue();
        $roiPercentage = $this->calculateOverallROI();
        
        // Target is 100% ROI (sales = investment)
        $targetSales = $totalInvestment;
        $difference = $totalSales - $targetSales;
        
        return [
            'amount' => abs($difference),
            'type' => $difference >= 0 ? 'profit' : 'kurang',
            'percentage_diff' => $roiPercentage - 100
        ];
    }
}
