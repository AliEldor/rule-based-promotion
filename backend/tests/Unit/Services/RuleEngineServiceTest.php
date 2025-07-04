<?php

namespace Tests\Unit\Services;

use App\Services\RuleEngineService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use InvalidArgumentException;

class RuleEngineServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }

    public function test_evaluate_rule_throws_exception_when_no_conditions()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule must have conditions.');

        $rule = ['id' => 1, 'name' => 'Test Rule'];
        $facts = ['customer' => ['id' => 1]];

        RuleEngineService::evaluateRule($rule, $facts);
    }

    public function test_evaluate_rule_throws_exception_when_empty_conditions()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule must have conditions.');

        $rule = ['id' => 1, 'name' => 'Test Rule', 'conditions' => []];
        $facts = ['customer' => ['id' => 1]];

        RuleEngineService::evaluateRule($rule, $facts);
    }

    public function test_convert_to_engine_format()
    {
        $rule = [
            'id' => 1,
            'name' => 'Test Rule',
            'conditions' => [
                ['field' => 'customer.segment', 'operator' => 'equal', 'value' => 'premium']
            ],
            'actions' => [
                ['type' => 'percentage_discount', 'value' => 10]
            ]
        ];
        $facts = ['line' => ['total' => 100]];

        $result = RuleEngineService::convertToEngineFormat($rule, $facts);

        $this->assertArrayHasKey('conditions', $result);
        $this->assertArrayHasKey('event', $result);
        $this->assertEquals('discount', $result['event']['type']);
        $this->assertEquals(1, $result['event']['params']['ruleId']);
        $this->assertEquals('Test Rule', $result['event']['params']['ruleName']);
        $this->assertEquals(100, $result['event']['params']['lineTotal']);
    }

    public function test_convert_conditions_with_string_json()
    {
        $rule = [
            'id' => 1,
            'name' => 'Test Rule',
            'conditions' => json_encode([
                ['field' => 'customer.segment', 'operator' => 'equal', 'value' => 'premium']
            ]),
            'actions' => []
        ];
        $facts = ['line' => ['total' => 100]];

        $result = RuleEngineService::convertToEngineFormat($rule, $facts);

        $this->assertArrayHasKey('conditions', $result);
        $this->assertArrayHasKey('all', $result['conditions']);
    }

    public function test_convert_conditions_with_invalid_json()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON in conditions');

        $rule = [
            'id' => 1,
            'name' => 'Test Rule',
            'conditions' => 'invalid json string',
            'actions' => []
        ];
        $facts = ['line' => ['total' => 100]];

        RuleEngineService::convertToEngineFormat($rule, $facts);
    }

    public function test_convert_conditions_with_non_array_conditions()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Conditions must be an array or valid JSON string');

        $rule = [
            'id' => 1,
            'name' => 'Test Rule',
            'conditions' => 123, // Not an array or string
            'actions' => []
        ];
        $facts = ['line' => ['total' => 100]];

        RuleEngineService::convertToEngineFormat($rule, $facts);
    }

    public function test_engine_format_includes_all_required_fields()
    {
        $rule = [
            'id' => 5,
            'name' => 'Complex Rule',
            'conditions' => [
                ['field' => 'customer.segment', 'operator' => 'equal', 'value' => 'premium'],
                ['field' => 'cart.total', 'operator' => 'greaterThan', 'value' => 100]
            ],
            'actions' => [
                ['type' => 'percentage_discount', 'value' => 15],
                ['type' => 'free_shipping']
            ]
        ];
        $facts = ['line' => ['total' => 250]];

        $result = RuleEngineService::convertToEngineFormat($rule, $facts);

        $this->assertEquals(5, $result['event']['params']['ruleId']);
        $this->assertEquals('Complex Rule', $result['event']['params']['ruleName']);
        $this->assertEquals(250, $result['event']['params']['lineTotal']);
        $this->assertIsArray($result['event']['params']['actions']);
        $this->assertArrayHasKey('all', $result['conditions']);
    }

    public function test_condition_conversion_handles_different_operators()
    {
        $rule = [
            'id' => 1,
            'name' => 'Test Rule',
            'conditions' => [
                ['field' => 'customer.age', 'operator' => 'greaterThan', 'value' => 18],
                ['field' => 'customer.segment', 'operator' => 'equal', 'value' => 'premium'],
                ['field' => 'cart.total', 'operator' => 'lessThan', 'value' => 500]
            ],
            'actions' => []
        ];
        $facts = ['line' => ['total' => 100]];

        $result = RuleEngineService::convertToEngineFormat($rule, $facts);

        $this->assertArrayHasKey('conditions', $result);
        $this->assertArrayHasKey('all', $result['conditions']);
        $this->assertCount(3, $result['conditions']['all']);
    }

    public function test_action_conversion_handles_different_types()
    {
        $rule = [
            'id' => 1,
            'name' => 'Test Rule',
            'conditions' => [
                ['field' => 'customer.segment', 'operator' => 'equal', 'value' => 'premium']
            ],
            'actions' => [
                ['type' => 'PERCENT_DISCOUNT', 'parameters' => ['percent' => 10]],
                ['type' => 'FIXED_DISCOUNT', 'parameters' => ['amount' => 5]],
                ['type' => 'FREE_UNITS', 'value' => 1]
            ]
        ];
        $facts = ['line' => ['total' => 100]];

        $result = RuleEngineService::convertToEngineFormat($rule, $facts);

        $actions = $result['event']['params']['actions'];
        $this->assertCount(3, $actions);
        $this->assertEquals('percent', $actions[0]['type']);
        $this->assertEquals('fixed', $actions[1]['type']);
        $this->assertEquals('free_units', $actions[2]['type']);
    }
}
