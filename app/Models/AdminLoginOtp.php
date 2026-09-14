<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLoginOtp extends Model
{
    protected $fillable = [
        'challenge_id',
        'user_id',
        'code_hash',
        'expires_at',
        'last_sent_at',
        'attempts',
        'send_count',
        'used_at',
        'cancelled_at',
        'remember',
        'intended_url',
        'ip_address',
        'user_agent',
    ];

    protected $hidden = [
        'code_hash',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_sent_at' => 'datetime',
            'used_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'remember' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('used_at')->whereNull('cancelled_at');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
