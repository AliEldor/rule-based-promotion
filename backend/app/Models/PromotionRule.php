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

    
}
