<?php

namespace Tests\Unit\Services;

use App\Services\RuleService;
use App\Models\PromotionRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_rule_successfully()
    {
        $ruleData = [
            'name' => 'Test Rule',
            'description' => 'A test rule',
            'salience' => 10,
            'stackable' => true,
            'is_active' => true,
            'conditions' => [
                ['field' => 'customer.segment', 'operator' => 'equal', 'value' => 'premium']
            ],
            'actions' => [
                ['type' => 'percentage_discount', 'value' => 15]
            ]
        ];

        $rule = RuleService::createRule($ruleData);

        $this->assertInstanceOf(PromotionRule::class, $rule);
        $this->assertEquals('Test Rule', $rule->name);
        $this->assertEquals('A test rule', $rule->description);
        $this->assertEquals(10, $rule->salience);
        $this->assertTrue($rule->stackable);
        $this->assertTrue($rule->is_active);
        $this->assertDatabaseHas('promotion_rules', ['name' => 'Test Rule']);
    }

    public function test_update_rule_successfully()
    {
        $rule = PromotionRule::create([
            'name' => 'Original Rule',
            'description' => 'Original description',
            'salience' => 5,
            'stackable' => false,
            'is_active' => true,
            'conditions' => [],
            'actions' => []
        ]);

        $updateData = [
            'name' => 'Updated Rule',
            'description' => 'Updated description',
            'salience' => 15
        ];

        $updatedRule = RuleService::updateRule($rule->id, $updateData);

        $this->assertEquals('Updated Rule', $updatedRule->name);
        $this->assertEquals('Updated description', $updatedRule->description);
        $this->assertEquals(15, $updatedRule->salience);
        $this->assertDatabaseHas('promotion_rules', [
            'id' => $rule->id,
            'name' => 'Updated Rule'
        ]);
    }

    public function test_update_rule_throws_exception_when_not_found()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Rule not found');

        RuleService::updateRule(999, ['name' => 'Updated Rule']);
    }

    public function test_delete_rule_successfully()
    {
        $rule = PromotionRule::create([
            'name' => 'Rule to Delete',
            'description' => 'This rule will be deleted',
            'salience' => 10,
            'stackable' => true,
            'is_active' => true,
            'conditions' => [],
            'actions' => []
        ]);

        $result = RuleService::deleteRule($rule->id);

        $this->assertEquals(['message' => 'Successfully deleted'], $result);
        $this->assertDatabaseMissing('promotion_rules', ['id' => $rule->id]);
    }

    public function test_delete_rule_throws_exception_when_not_found()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Rule not found');

        RuleService::deleteRule(999);
    }

    public function test_get_single_rule_successfully()
    {
        $rule = PromotionRule::create([
            'name' => 'Single Rule',
            'description' => 'Get this rule',
            'salience' => 10,
            'stackable' => true,
            'is_active' => true,
            'conditions' => [],
            'actions' => []
        ]);

        $retrievedRule = RuleService::getSingleRule($rule->id);

        $this->assertInstanceOf(PromotionRule::class, $retrievedRule);
        $this->assertEquals('Single Rule', $retrievedRule->name);
        $this->assertEquals($rule->id, $retrievedRule->id);
    }

    public function test_get_single_rule_throws_exception_when_not_found()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Rule not found');

        RuleService::getSingleRule(999);
    }

    public function test_get_all_rules_with_pagination()
    {
       
        for ($i = 1; $i <= 15; $i++) {
            PromotionRule::create([
                'name' => "Rule $i",
                'description' => "Description $i",
                'salience' => $i,
                'stackable' => true,
                'is_active' => true,
                'conditions' => [],
                'actions' => []
            ]);
        }

        $result = RuleService::getAllRules(0);
        $this->assertEquals(10, $result['count']);
        $this->assertCount(10, $result['data']);

        $result = RuleService::getAllRules(1);
        $this->assertEquals(5, $result['count']);
        $this->assertCount(5, $result['data']);
    }

    public function test_get_active_rules_only_returns_active_and_valid_rules()
    {
        PromotionRule::create([
            'name' => 'Active Rule',
            'description' => 'This rule is active',
            'salience' => 10,
            'stackable' => true,
            'is_active' => true,
            'conditions' => [],
            'actions' => [],
            'valid_from' => now()->subDays(5),
            'valid_until' => now()->addDays(5)
        ]);

        PromotionRule::create([
            'name' => 'Inactive Rule',
            'description' => 'This rule is inactive',
            'salience' => 5,
            'stackable' => true,
            'is_active' => false,
            'conditions' => [],
            'actions' => [],
            'valid_from' => now()->subDays(5),
            'valid_until' => now()->addDays(5)
        ]);

        PromotionRule::create([
            'name' => 'Expired Rule',
            'description' => 'This rule is expired',
            'salience' => 15,
            'stackable' => true,
            'is_active' => true,
            'conditions' => [],
            'actions' => [],
            'valid_from' => now()->subDays(30),
            'valid_until' => now()->subDays(10)
        ]);

        $activeRules = RuleService::getActiveRules();

        $this->assertCount(1, $activeRules);
        $this->assertEquals('Active Rule', $activeRules[0]['name']);
    }

    public function test_get_active_rules_returns_correct_structure()
    {
        $rule = PromotionRule::create([
            'name' => 'Test Rule',
            'description' => 'Test description',
            'salience' => 10,
            'stackable' => true,
            'is_active' => true,
            'conditions' => [
                ['field' => 'customer.segment', 'operator' => 'equal', 'value' => 'premium']
            ],
            'actions' => [
                ['type' => 'percentage_discount', 'value' => 15]
            ],
            'valid_from' => now()->subDays(5),
            'valid_until' => now()->addDays(5)
        ]);

        $activeRules = RuleService::getActiveRules();

        $this->assertCount(1, $activeRules);
        $ruleData = $activeRules[0];

        $this->assertArrayHasKey('id', $ruleData);
        $this->assertArrayHasKey('name', $ruleData);
        $this->assertArrayHasKey('description', $ruleData);
        $this->assertArrayHasKey('salience', $ruleData);
        $this->assertArrayHasKey('stackable', $ruleData);
        $this->assertArrayHasKey('is_active', $ruleData);
        $this->assertArrayHasKey('conditions', $ruleData);
        $this->assertArrayHasKey('actions', $ruleData);
        $this->assertArrayHasKey('valid_from', $ruleData);
        $this->assertArrayHasKey('valid_until', $ruleData);
        $this->assertArrayHasKey('created_at', $ruleData);
        $this->assertArrayHasKey('updated_at', $ruleData);

        $this->assertEquals($rule->id, $ruleData['id']);
        $this->assertEquals('Test Rule', $ruleData['name']);
        $this->assertEquals('Test description', $ruleData['description']);
        $this->assertEquals(10, $ruleData['salience']);
        $this->assertTrue($ruleData['stackable']);
        $this->assertTrue($ruleData['is_active']);
    }

    public function test_get_active_rules_orders_by_salience()
    {
        PromotionRule::create([
            'name' => 'Low Priority',
            'salience' => 5,
            'is_active' => true,
            'conditions' => [],
            'actions' => [],
            'valid_from' => now()->subDays(5),
            'valid_until' => now()->addDays(5)
        ]);

        PromotionRule::create([
            'name' => 'High Priority',
            'salience' => 20,
            'is_active' => true,
            'conditions' => [],
            'actions' => [],
            'valid_from' => now()->subDays(5),
            'valid_until' => now()->addDays(5)
        ]);

        PromotionRule::create([
            'name' => 'Medium Priority',
            'salience' => 10,
            'is_active' => true,
            'conditions' => [],
            'actions' => [],
            'valid_from' => now()->subDays(5),
            'valid_until' => now()->addDays(5)
        ]);

        $activeRules = RuleService::getActiveRules();

        $this->assertCount(3, $activeRules);
        $this->assertEquals('Low Priority', $activeRules[0]['name']);
        $this->assertEquals('Medium Priority', $activeRules[1]['name']);
        $this->assertEquals('High Priority', $activeRules[2]['name']);
    }
}
