<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomerSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_id',
        'title',
        'scheduled_date',
        'status',
        'is_recurring',
        'recurring_days',
        'last_notified_at',
        'notes'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'last_notified_at' => 'datetime',
        'is_recurring' => 'boolean'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDueToday($query)
    {
        return $query->whereDate('scheduled_date', '=', now()->toDateString())
                    ->where('status', 'pending');
    }

    public function scopeDueThisWeek($query)
    {
        return $query->whereBetween('scheduled_date', [
            now()->addDay()->startOfDay(), // Mulai dari besok
            now()->endOfWeek()->endOfDay()
        ])->where('status', 'pending');
    }


    public function scopeOverdue($query)
    {
        return $query->where('scheduled_date', '<', now()->startOfDay())
                    ->where('status', 'pending');
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return $this->scheduled_date->lt(now()->startOfDay()) && $this->status === 'pending';
    }

    public function getIsDueTodayAttribute()
    {
        return $this->scheduled_date->isToday() && $this->status === 'pending';
    }

    public function getIsDueThisWeekAttribute()
    {
        return $this->scheduled_date->gt(now()->endOfDay()) 
            && $this->scheduled_date->lte(now()->endOfWeek()) 
            && $this->status === 'pending';
    }

    public function getDaysUntilDueAttribute()
    {
        return (int) now()->diffInDays($this->scheduled_date, false);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'notified' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800'
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }


    // Methods
    public function markAsNotified()
    {
        $this->update([
            'status' => 'notified',
            'last_notified_at' => now()
        ]);
    }

    public function markAsCompleted()
    {
        $this->update(['status' => 'completed']);
        
        // If recurring, create next schedule
        if ($this->is_recurring && $this->recurring_days) {
            $this->createNextRecurringSchedule();
        }
    }

    public function createNextRecurringSchedule()
    {
        if ($this->is_recurring && $this->recurring_days) {
            $nextDate = $this->scheduled_date->addDays($this->recurring_days);
            
            static::create([
                'customer_id' => $this->customer_id,
                'product_id' => $this->product_id,
                'title' => $this->title ?? 'Follow up recurring',
                'scheduled_date' => $nextDate,
                'status' => 'pending',
                'is_recurring' => true,
                'recurring_days' => $this->recurring_days,
                'notes' => $this->notes
            ]);
        }
    }
}
