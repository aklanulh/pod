<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'kso_item_id',
        'kso_support_item_id',
        'equipment_name',
        'equipment_type',
        'last_maintenance_date',
        'next_maintenance_date',
        'maintenance_type',
        'description',
        'status',
        'notes',
        'cost',
        'technician',
        'technician_notes',
    ];

    protected $casts = [
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    // Relationships
    public function ksoItem()
    {
        return $this->belongsTo(KsoItem::class);
    }

    public function ksoSupportItem()
    {
        return $this->belongsTo(KsoSupportItem::class);
    }

    // Scopes
    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('next_maintenance_date', '>=', now())
            ->where('next_maintenance_date', '<=', now()->addDays($days))
            ->where('status', 'scheduled');
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_maintenance_date', '<', now())
            ->where('status', '!=', 'completed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper methods
    public function isOverdue()
    {
        return $this->next_maintenance_date->isPast() && $this->status !== 'completed';
    }

    public function isUpcoming($days = 30)
    {
        return $this->next_maintenance_date->between(now(), now()->addDays($days)) && $this->status === 'scheduled';
    }

    public function getDaysUntilMaintenance()
    {
        return now()->diffInDays($this->next_maintenance_date, false);
    }

    public function getMaintenanceStatusAttribute()
    {
        if ($this->status === 'completed') {
            return 'completed';
        } elseif ($this->isOverdue()) {
            return 'overdue';
        } elseif ($this->isUpcoming()) {
            return 'upcoming';
        } else {
            return 'scheduled';
        }
    }
}
