<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PromotionRule extends Model
{
    protected $fillable = [
        'name',
        'description',
        'salience',
        'stackable',
        'is_active',
        'conditions',
        'actions',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'salience' => 'integer',
        'stackable' => 'boolean',
        'is_active' => 'boolean',
        'conditions' => 'array',
        'actions' => 'array',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    public function ruleApplications(): HasMany
    {
        return $this->hasMany(RuleApplication::class, 'rule_id');
    }

    
    //   Scope a query to only include active rules
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }


    // Scope a query to only include rules valid at a given time
     public function scopeValidAt(Builder $query, ?Carbon $dateTime = null): Builder
    {
        $dateTime = $dateTime ?? now();

        return $query->where(function (Builder $query) use ($dateTime): void {
            $query->where('valid_from', '<=', $dateTime)
                  ->orWhereNull('valid_from');
        })->where(function (Builder $query) use ($dateTime): void {
            $query->where('valid_until', '>=', $dateTime)
                  ->orWhereNull('valid_until');
        });
    }


    //  priority
     public function scopeOrderBySalience(Builder $query): Builder
    {
        return $query->orderBy('salience', 'asc');
    }

    public function isValidNow(): bool
    {
        $now = now();

        if ($this->valid_from && $this->valid_from->greaterThan($now)) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->lessThan($now)) {
            return false;
        }

        return true;
    }

    public function isStackable(): bool
    {
        return $this->stackable;
    }
}
