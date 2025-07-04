<?php

namespace App\Services;

use App\Models\PromotionRule;
use Exception;

class RuleService
{
    public static function createRule(array $ruleData): PromotionRule
    {
        return PromotionRule::create($ruleData);
    }

    public static function updateRule(int $id, array $ruleData): PromotionRule
    {
        $rule = PromotionRule::find($id);

        if (!$rule) {
            throw new Exception('Rule not found');
        }

        $rule->update($ruleData);
        return $rule->fresh();
    }

    public static function deleteRule(int $id): array
    {
        $rule = PromotionRule::find($id);

        if (!$rule) {
            throw new Exception('Rule not found');
        }

        $rule->delete();
        return ['message' => 'Successfully deleted'];
    }

    public static function getSingleRule(int $id): PromotionRule
    {
        $rule = PromotionRule::find($id);

        if (!$rule) {
            throw new Exception('Rule not found');
        }

        return $rule;
    }

    public static function getAllRules(int $page = 0): array
    {
        $rules = PromotionRule::skip($page * 10)
            ->limit(10)
            ->get();

        return [
            'count' => $rules->count(),
            'data' => $rules,
        ];
    }

    public static function getActiveRules(): array
    {
        $rules = PromotionRule::active()
            ->validAt()
            ->orderBySalience()
            ->get();

        return $rules->map(function ($rule) {
            return [
                'id' => $rule->id,
                'name' => $rule->name,
                'description' => $rule->description,
                'salience' => $rule->salience,
                'stackable' => $rule->stackable,
                'is_active' => $rule->is_active,
                'conditions' => $rule->conditions, 
                'actions' => $rule->actions,       
                'valid_from' => $rule->valid_from,
                'valid_until' => $rule->valid_until,
                'created_at' => $rule->created_at,
                'updated_at' => $rule->updated_at,
            ];
        })->toArray();
    }
}
