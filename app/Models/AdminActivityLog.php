<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AdminActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'data',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function logActivity(string $action, string $module, string $description, array $data = null): void
    {
        if (Auth::check()) {
            self::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'data' => $data,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }
    }

    public function getActionBadgeAttribute(): string
    {
        $badges = [
            'create' => 'bg-green-100 text-green-800',
            'update' => 'bg-blue-100 text-blue-800',
            'delete' => 'bg-red-100 text-red-800',
            'view' => 'bg-gray-100 text-gray-800',
            'login' => 'bg-purple-100 text-purple-800',
            'logout' => 'bg-orange-100 text-orange-800',
        ];

        return $badges[$this->action] ?? 'bg-gray-100 text-gray-800';
    }

    public function getActionIconAttribute(): string
    {
        $icons = [
            'create' => 'fas fa-plus',
            'update' => 'fas fa-edit',
            'delete' => 'fas fa-trash',
            'view' => 'fas fa-eye',
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
        ];

        return $icons[$this->action] ?? 'fas fa-info';
    }
}
