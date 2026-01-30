<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'address',
        'is_active',
        'notes'
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function customerSchedules()
    {
        return $this->hasMany(CustomerSchedule::class);
    }

    public function ksoItems()
    {
        return $this->hasMany(KsoItem::class);
    }

    public function stockOutDrafts()
    {
        return $this->hasMany(StockOutDraft::class);
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

    /**
     * Get customers that can be merged (potential duplicates)
     */
    public static function getPotentialDuplicates()
    {
        // Find customers with similar names (case insensitive)
        $duplicates = collect();

        // Get all customers and group by similar names
        $customers = self::orderBy('name')->get();
        $groupedByName = [];

        foreach ($customers as $customer) {
            $normalizedName = strtolower(trim($customer->name));
            $groupedByName[$normalizedName][] = $customer;
        }

        // Filter groups with more than 1 customer
        foreach ($groupedByName as $group) {
            if (count($group) > 1) {
                $duplicates->push([
                    'group_type' => 'exact_match',
                    'customers' => collect($group),
                    'reason' => 'Nama customer identik (case insensitive)'
                ]);
            }
        }

        // Find customers with similar phone numbers
        $groupedByPhone = [];
        foreach ($customers as $customer) {
            if ($customer->phone && !empty(trim($customer->phone))) {
                $normalizedPhone = preg_replace('/[^0-9]/', '', trim($customer->phone));
                if (!empty($normalizedPhone)) {
                    $groupedByPhone[$normalizedPhone][] = $customer;
                }
            }
        }

        // Filter phone groups with more than 1 customer
        foreach ($groupedByPhone as $phone => $group) {
            if (count($group) > 1) {
                $duplicates->push([
                    'group_type' => 'similar_phone',
                    'customers' => collect($group),
                    'reason' => "Nomor telepon sama: {$phone}"
                ]);
            }
        }

        return $duplicates;
    }

    /**
     * Merge this customer into target customer
     */
    public function mergeInto(Customer $targetCustomer, string $reason = null, $mergedBy = null)
    {
        if ($this->id === $targetCustomer->id) {
            throw new \Exception('Cannot merge customer into itself');
        }

        DB::transaction(function () use ($targetCustomer, $reason, $mergedBy) {
            // 1. Move KSO Items
            DB::table('kso_items')
                ->where('customer_id', $this->id)
                ->update(['customer_id' => $targetCustomer->id]);

            // 2. Move Stock Movements
            DB::table('stock_movements')
                ->where('customer_id', $this->id)
                ->update([
                    'customer_id' => $targetCustomer->id,
                    'customer_name' => $targetCustomer->name
                ]);

            // 3. Move Customer Schedules
            DB::table('customer_schedules')
                ->where('customer_id', $this->id)
                ->update(['customer_id' => $targetCustomer->id]);

            // 4. Move Stock Out Drafts
            DB::table('stock_out_drafts')
                ->where('customer_id', $this->id)
                ->update([
                    'customer_id' => $targetCustomer->id,
                    'customer_name' => $targetCustomer->name
                ]);

            // 5. Record the merge
            CustomerMerge::create([
                'source_customer_id' => $this->id,
                'target_customer_id' => $targetCustomer->id,
                'reason' => $reason,
                'merged_by' => $mergedBy?->id ?? auth()->id(),
            ]);

            // 6. Delete the source customer
            $this->delete();
        });

        return $targetCustomer;
    }

    /**
     * Get merge history
     */
    public function mergeHistory()
    {
        return $this->hasMany(CustomerMerge::class, 'source_customer_id')
            ->orWhere('target_customer_id', $this->id)
            ->with(['sourceCustomer', 'targetCustomer', 'mergedBy'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Check if customer can be merged (not already merged)
     */
    public function canBeMerged(): bool
    {
        return !$this->mergeHistory()->exists();
    }
}
