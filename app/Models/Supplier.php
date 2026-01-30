<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
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

    /**
     * Get suppliers that can be merged (potential duplicates)
     */
    public static function getPotentialDuplicates()
    {
        // Find suppliers with similar names (case insensitive)
        $duplicates = collect();

        // Get all suppliers and group by similar names
        $suppliers = self::orderBy('name')->get();
        $groupedByName = [];

        foreach ($suppliers as $supplier) {
            $normalizedName = strtolower(trim($supplier->name));
            $groupedByName[$normalizedName][] = $supplier;
        }

        // Filter groups with more than 1 supplier
        foreach ($groupedByName as $group) {
            if (count($group) > 1) {
                $duplicates->push([
                    'group_type' => 'exact_match',
                    'suppliers' => collect($group),
                    'reason' => 'Nama supplier identik (case insensitive)'
                ]);
            }
        }

        // Find suppliers with similar phone numbers
        $groupedByPhone = [];
        foreach ($suppliers as $supplier) {
            if ($supplier->phone && !empty(trim($supplier->phone))) {
                $normalizedPhone = preg_replace('/[^0-9]/', '', trim($supplier->phone));
                if (!empty($normalizedPhone)) {
                    $groupedByPhone[$normalizedPhone][] = $supplier;
                }
            }
        }

        // Filter phone groups with more than 1 supplier
        foreach ($groupedByPhone as $phone => $group) {
            if (count($group) > 1) {
                $duplicates->push([
                    'group_type' => 'similar_phone',
                    'suppliers' => collect($group),
                    'reason' => "Nomor telepon sama: {$phone}"
                ]);
            }
        }

        return $duplicates;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}
