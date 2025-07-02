<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'email',
        'type',
        'loyalty_tier',
        'orders_count',
        'city',
    ];

     protected $casts = [
        'orders_count' => 'integer',
    ];

    public function ruleApplications(): HasMany
    {
        return $this->hasMany(RuleApplication::class);
    }
}
