<?php

namespace Database\Seeders;

use App\Models\PromotionRule;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PromotionRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'id' => 100,
                'name' => 'Buy 5 Get 1 Free on SKU 123',
                'description' => 'Get 1 free unit when purchasing 5 or more of Widget A',
                'salience' => 10,
                'stackable' => false,
                'is_active' => true,
                'conditions' => [
                    [
                        'operator' => 'AND',
                        'conditions' => [
                            [
                                'field' => 'line.productId',
                                'operator' => 'EQUALS',
                                'value' => 123
                            ],
                            [
                                'field' => 'line.quantity',
                                'operator' => 'GREATER_THAN_OR_EQUAL',
                                'value' => 5
                            ]
                        ]
                    ]
                ],
                'actions' => [
                    [
                        'type' => 'FREE_UNITS',
                        'parameters' => [
                            'quantity' => 1
                        ]
                    ]
                ],
                'valid_from' => null,
                'valid_until' => null,
            ],
            [
                'id' => 101,
                'name' => 'Tiered Discount SKU 456',
                'description' => 'Tiered percentage discount on Gadget B',
                'salience' => 20,
                'stackable' => true,
                'is_active' => true,
                'conditions' => [
                    [
                        'operator' => 'AND',
                        'conditions' => [
                            [
                                'field' => 'line.productId',
                                'operator' => 'EQUALS',
                                'value' => 456
                            ],
                            [
                                'field' => 'line.quantity',
                                'operator' => 'GREATER_THAN_OR_EQUAL',
                                'value' => 5
                            ]
                        ]
                    ]
                ],
                'actions' => [
                    [
                        'type' => 'TIERED_PERCENT',
                        'parameters' => [
                            'tiers' => [
                                [
                                    'min_quantity' => 5,
                                    'max_quantity' => 9,
                                    'percent' => 5
                                ],
                                [
                                    'min_quantity' => 10,
                                    'max_quantity' => null,
                                    'percent' => 10
                                ]
                            ]
                        ]
                    ]
                ],
                'valid_from' => null,
                'valid_until' => null,
            ],
            [
                'id' => 102,
                'name' => '20% off Electronics',
                'description' => '20% discount on all electronics products',
                'salience' => 15,
                'stackable' => true,
                'is_active' => true,
                'conditions' => [
                    [
                        'field' => 'line.categoryId',
                        'operator' => 'EQUALS',
                        'value' => 10
                    ]
                ],
                'actions' => [
                    [
                        'type' => 'PERCENT_DISCOUNT',
                        'parameters' => [
                            'percent' => 20
                        ]
                    ]
                ],
                'valid_from' => null,
                'valid_until' => null,
            ],
            [
                'id' => 103,
                'name' => '10% off for Restaurants',
                'description' => '10% discount for restaurant customers',
                'salience' => 30,
                'stackable' => true,
                'is_active' => true,
                'conditions' => [
                    [
                        'field' => 'customer.type',
                        'operator' => 'EQUALS',
                        'value' => 'restaurants'
                    ]
                ],
                'actions' => [
                    [
                        'type' => 'PERCENT_DISCOUNT',
                        'parameters' => [
                            'percent' => 10
                        ]
                    ]
                ],
                'valid_from' => null,
                'valid_until' => null,
            ],
            [
                'id' => 104,
                'name' => '5% off apple.com Corporate',
                'description' => '5% discount for Apple corporate customers',
                'salience' => 25,
                'stackable' => true,
                'is_active' => true,
                'conditions' => [
                    [
                        'field' => 'customer.email',
                        'operator' => 'ENDS_WITH',
                        'value' => '@apple.com'
                    ]
                ],
                'actions' => [
                    [
                        'type' => 'PERCENT_DISCOUNT',
                        'parameters' => [
                            'percent' => 5
                        ]
                    ]
                ],
                'valid_from' => null,
                'valid_until' => null,
            ],
            [
                'id' => 105,
                'name' => 'Flash Sale SKU 789',
                'description' => 'Time-limited 25% discount on Flash Deal C',
                'salience' => 5,
                'stackable' => false,
                'is_active' => true,
                'conditions' => [
                    [
                        'operator' => 'AND',
                        'conditions' => [
                            [
                                'field' => 'line.productId',
                                'operator' => 'EQUALS',
                                'value' => 789
                            ],
                            [
                                'field' => 'system.currentDateTime',
                                'operator' => 'LESS_THAN',
                                'value' => '2025-07-01T00:00:00Z'
                            ]
                        ]
                    ]
                ],
                'actions' => [
                    [
                        'type' => 'PERCENT_DISCOUNT',
                        'parameters' => [
                            'percent' => 25
                        ]
                    ]
                ],
                'valid_from' => null,
                'valid_until' => '2025-07-01 00:00:00',
            ],
            [
                'id' => 106,
                'name' => 'Clearance Category Obsolete',
                'description' => '50% discount on all clearance items',
                'salience' => 40,
                'stackable' => true,
                'is_active' => true,
                'conditions' => [
                    [
                        'field' => 'line.categoryId',
                        'operator' => 'EQUALS',
                        'value' => 99
                    ]
                ],
                'actions' => [
                    [
                        'type' => 'PERCENT_DISCOUNT',
                        'parameters' => [
                            'percent' => 50
                        ]
                    ]
                ],
                'valid_from' => null,
                'valid_until' => null,
            ],
            [
                'id' => 107,
                'name' => 'Gold Tier Multiplier',
                'description' => '5% additional discount for gold loyalty customers',
                'salience' => 35,
                'stackable' => true,
                'is_active' => true,
                'conditions' => [
                    [
                        'field' => 'customer.loyaltyTier',
                        'operator' => 'EQUALS',
                        'value' => 'gold'
                    ]
                ],
                'actions' => [
                    [
                        'type' => 'PERCENT_DISCOUNT',
                        'parameters' => [
                            'percent' => 5
                        ]
                    ]
                ],
                'valid_from' => null,
                'valid_until' => null,
            ],
            [
                'id' => 108,
                'name' => 'First Purchase SKU 555',
                'description' => '15% discount on first purchase of Intro SKU D',
                'salience' => 12,
                'stackable' => true,
                'is_active' => true,
                'conditions' => [
                    [
                        'operator' => 'AND',
                        'conditions' => [
                            [
                                'field' => 'line.productId',
                                'operator' => 'EQUALS',
                                'value' => 555
                            ],
                            [
                                'field' => 'customer.ordersCount',
                                'operator' => 'EQUALS',
                                'value' => 0
                            ]
                        ]
                    ]
                ],
                'actions' => [
                    [
                        'type' => 'PERCENT_DISCOUNT',
                        'parameters' => [
                            'percent' => 15
                        ]
                    ]
                ],
                'valid_from' => null,
                'valid_until' => null,
            ],
            [
                'id' => 109,
                'name' => 'City Promo (Jeddah)',
                'description' => '3% discount for customers in Jeddah',
                'salience' => 18,
                'stackable' => true,
                'is_active' => true,
                'conditions' => [
                    [
                        'field' => 'customer.city',
                        'operator' => 'EQUALS',
                        'value' => 'Jeddah'
                    ]
                ],
                'actions' => [
                    [
                        'type' => 'PERCENT_DISCOUNT',
                        'parameters' => [
                            'percent' => 3
                        ]
                    ]
                ],
                'valid_from' => null,
                'valid_until' => null,
            ],
        ];

        foreach ($rules as $rule) {
            PromotionRule::updateOrCreate(
                ['id' => $rule['id']],
                [
                    'name' => $rule['name'],
                    'description' => $rule['description'],
                    'salience' => $rule['salience'],
                    'stackable' => $rule['stackable'],
                    'is_active' => $rule['is_active'],
                    'conditions' => json_encode($rule['conditions']),
                    'actions' => json_encode($rule['actions']),
                    'valid_from' => $rule['valid_from'],
                    'valid_until' => $rule['valid_until'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
