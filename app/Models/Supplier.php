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
        'address'
    ];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
