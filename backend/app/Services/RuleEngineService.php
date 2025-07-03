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
                    'actions' => $rule['actions'],
                    'lineTotal' => $facts['line']['total']
                ]
            ]
        ];
    }

    public static function testEngine(): array
    {
        $response = Http::timeout(5)->get(self::$engineUrl . '/health');

        if ($response->failed()) {
            throw new \Exception('Rule engine service is not accessible.');
        }

        return $response->json();
    }

   private static function convertConditions(array $conditions): array
{
    $converted = [
        'all' => []
    ];

    foreach ($conditions as $condition) {
        if (empty($condition['field']) || empty($condition['operator'])) {
            throw new InvalidArgumentException('Each condition must have field and operator.');
        }

        $fieldParts = explode('.', $condition['field']);
        $fact = $fieldParts[0]; 
        
        $engineCondition = [
            'fact' => $fact,
            'operator' => self::mapOperator($condition['operator']),
            'value' => $condition['value']
        ];

        // add path if theres a nested property
        if (count($fieldParts) > 1) {
            $engineCondition['path'] = '$.' . $fieldParts[1]; 
        }

        $converted['all'][] = $engineCondition;
    }

    return $converted;
}

    private static function mapOperator(string $operator): string
    {
        return match ($operator) {
            'equals', '==' => 'equal',
            'not_equals', '!=' => 'notEqual',
            'greater_than', '>' => 'greaterThan',
            'greater_than_equals', '>=' => 'greaterThanInclusive',
            'less_than', '<' => 'lessThan',
            'less_than_equals', '<=' => 'lessThanInclusive',
            'contains' => 'contains',
            'starts_with' => 'startsWith',
            'ends_with' => 'endsWith',
            'in' => 'in',
            'not_in' => 'notIn',
            default => throw new InvalidArgumentException("Unsupported operator: {$operator}")
        };
    }
}