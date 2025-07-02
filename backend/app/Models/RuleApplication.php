<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RuleApplication extends Model
{
    protected $fillable = [
        'rule_id',
        'customer_id',
        'order_reference',
        'line_item_data',
        'customer_data',
        'discount_amount',
    ];

    protected $casts = [
        'rule_id' => 'integer',
        'customer_id' => 'integer',
        'line_item_data' => 'array',
        'customer_data' => 'array',
        'discount_amount' => 'decimal:2',
    ];

     public function promotionRule(): BelongsTo
    {
        return $this->belongsTo(PromotionRule::class, 'rule_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
