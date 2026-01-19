<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PONumber extends Model
{
    protected $table = 'po_numbers';

    protected $fillable = [
        'po_number',
        'month',
        'year',
        'last_number'
    ];

    protected $casts = [
        'po_number' => 'string',
        'month' => 'integer',
        'year' => 'integer',
        'last_number' => 'integer'
    ];

    /**
     * Get the last PO number for a specific month and year
     */
    public static function getLastPO($month, $year)
    {
        return self::where('month', $month)
            ->where('year', $year)
            ->orderBy('last_number', 'desc')
            ->first();
    }

    /**
     * Save a new PO number
     */
    public static function savePO($poNumber, $month, $year, $lastNumber)
    {
        return self::updateOrCreate(
            [
                'month' => $month,
                'year' => $year
            ],
            [
                'po_number' => $poNumber,
                'last_number' => $lastNumber
            ]
        );
    }
}
