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

   
}