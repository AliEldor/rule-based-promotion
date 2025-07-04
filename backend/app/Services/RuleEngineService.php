<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class RuleEngineService
{
    private static string $engineUrl = 'http://localhost:3000';

    public static function evaluateRule(array $rule, array $facts): array
    {
        if (empty($rule['conditions'])) {
            throw new InvalidArgumentException('Rule must have conditions.');
        }

        try {
            $engineRule = self::convertToEngineFormat($rule, $facts);

            $response = Http::timeout(10)->post(self::$engineUrl . '/evaluate', [
                'rule' => $engineRule,
                'facts' => $facts
            ]);

            if ($response->failed()) {
                throw new \Exception('Rule engine service unavailable: ' . $response->body());
            }

            $result = $response->json();

            return [
                'matched' => $result['matched'] ?? false,
                'discount' => $result['discount'] ?? 0,
                'metadata' => $result['metadata'] ?? []
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception('Failed to connect to rule engine service: ' . $e->getMessage());
        } catch (\Illuminate\Http\Client\RequestException $e) {
            throw new \Exception('Rule engine request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Re-throw any other exceptions with context
            throw new \Exception('Rule evaluation failed: ' . $e->getMessage());
        }
    }

    public static function convertToEngineFormat(array $rule, array $facts): array
    {
        return [
            'conditions' => self::convertConditions($rule['conditions']),
            'event' => [
                'type' => 'discount',
                'params' => [
                    'ruleId' => $rule['id'],
                    'ruleName' => $rule['name'],
                    'actions' => self::convertActions($rule['actions']),
                    'lineTotal' => $facts['line']['total']
                ]
            ]
        ];
    }

    public static function testEngine(): array
    {
        try {
            $response = Http::timeout(5)->get(self::$engineUrl . '/health');

            if ($response->failed()) {
                throw new \Exception('Rule engine service is not accessible.');
            }

            return $response->json();
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \Exception('Failed to connect to rule engine service: ' . $e->getMessage());
        } catch (\Illuminate\Http\Client\RequestException $e) {
            throw new \Exception('Rule engine health check failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Rule engine test failed: ' . $e->getMessage());
        }
    }

    private static function convertConditions($conditions): array
    {
        if (is_string($conditions)) {
            $conditions = json_decode($conditions, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON in conditions: ' . json_last_error_msg());
            }
        }

        // make sure conditions is an array
        if (!is_array($conditions)) {
            throw new \InvalidArgumentException('Conditions must be an array or valid JSON string');
        }

        $converted = [
            'all' => []
        ];

        foreach ($conditions as $condition) {
            if (!is_array($condition)) {
                continue;
            }

            if (
                isset($condition['operator']) && isset($condition['conditions']) &&
                in_array(strtoupper($condition['operator']), ['AND', 'OR'])
            ) {
                //  nested conditions
                foreach ($condition['conditions'] as $nestedCondition) {
                    if (
                        is_array($nestedCondition) &&
                        isset($nestedCondition['field']) &&
                        isset($nestedCondition['operator'])
                    ) {
                        $engineCondition = self::buildEngineCondition($nestedCondition);
                        if ($engineCondition) {
                            $converted['all'][] = $engineCondition;
                        }
                    }
                }
            } else {
                if (isset($condition['field']) && isset($condition['operator'])) {
                    $engineCondition = self::buildEngineCondition($condition);
                    if ($engineCondition) {
                        $converted['all'][] = $engineCondition;
                    }
                }
            }
        }

        return $converted;
    }

    private static function buildEngineCondition(array $condition): ?array
    {
        if (empty($condition['field']) || empty($condition['operator'])) {
            return null;
        }

        $fieldParts = explode('.', $condition['field']);
        $fact = $fieldParts[0];

        $engineCondition = [
            'fact' => $fact,
            'operator' => self::mapOperator($condition['operator']),
            'value' => $condition['value'] ?? null
        ];

        // add path if theres a nested property
        if (count($fieldParts) > 1) {
            $engineCondition['path'] = '$.' . $fieldParts[1];
        }

        return $engineCondition;
    }

    private static function mapOperator(string $operator): string
    {
        $operator = strtolower($operator);

        return match ($operator) {
            'equals', '==', 'equal' => 'equal',
            'not_equals', '!=', 'notequal' => 'notEqual',
            'greater_than', '>', 'greaterthan' => 'greaterThan',
            'greater_than_equals', 'greater_than_or_equal', '>=', 'greaterthaninclusive' => 'greaterThanInclusive',
            'less_than', '<', 'lessthan' => 'lessThan',
            'less_than_equals', 'less_than_or_equal', '<=', 'lessthaninclusive' => 'lessThanInclusive',
            'contains' => 'contains',
            'starts_with', 'startswith' => 'contains', // Using contains as fallback
            'ends_with', 'endswith' => 'contains', // Using contains as fallback
            'in' => 'in',
            'not_in', 'notin' => 'notIn',
            default => throw new InvalidArgumentException("Unsupported operator: {$operator}")
        };
    }

    private static function convertActions($actions): array
    {
        if (is_string($actions)) {
            $actions = json_decode($actions, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON in actions: ' . json_last_error_msg());
            }
        }

        if (!is_array($actions)) {
            throw new \InvalidArgumentException('Actions must be an array or valid JSON string');
        }

        $convertedActions = [];

        foreach ($actions as $action) {
            if (!is_array($action) || empty($action['type'])) {
                continue;
            }

            $convertedAction = [];

            switch (strtoupper($action['type'])) {
                case 'PERCENT_DISCOUNT':
                    $convertedAction = [
                        'type' => 'percent',
                        'value' => $action['parameters']['percent'] ?? 0
                    ];
                    break;

                case 'FIXED_DISCOUNT':
                    $convertedAction = [
                        'type' => 'fixed',
                        'value' => $action['parameters']['amount'] ?? 0
                    ];
                    break;

                case 'FREE_UNITS':
                    $convertedAction = [
                        'type' => 'free_units',
                        'value' => $action['value'] ?? 0
                    ];
                    break;

                case 'TIERED_PERCENT':
                    $convertedAction = [
                        'type' => 'tiered_percent',
                        'tiers' => $action['parameters']['tiers'] ?? []
                    ];
                    break;

                default:
                    continue 2;
            }

            $convertedActions[] = $convertedAction;
        }

        return $convertedActions;
    }
}
