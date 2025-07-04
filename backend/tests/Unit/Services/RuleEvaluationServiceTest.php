<?php

namespace Tests\Unit\Services;

use App\Services\RuleEvaluationService;
use Tests\TestCase;
use InvalidArgumentException;
use ReflectionClass;

class RuleEvaluationServiceTest extends TestCase
{
    public function test_evaluate_cart_throws_exception_when_missing_product_id()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('lineItem must have productId, quantity, and unitPrice.');

        $lineItem = [
            'quantity' => 2,
            'unitPrice' => 50.00
        ];
        $customer = ['id' => 1, 'email' => 'test@example.com'];

        RuleEvaluationService::evaluateCart($lineItem, $customer);
    }

    public function test_evaluate_cart_throws_exception_when_missing_quantity()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('lineItem must have productId, quantity, and unitPrice.');

        $lineItem = [
            'productId' => 1,
            'unitPrice' => 50.00
        ];
        $customer = ['id' => 1, 'email' => 'test@example.com'];

        RuleEvaluationService::evaluateCart($lineItem, $customer);
    }

    public function test_evaluate_cart_throws_exception_when_missing_unit_price()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('lineItem must have productId, quantity, and unitPrice.');

        $lineItem = [
            'productId' => 1,
            'quantity' => 2
        ];
        $customer = ['id' => 1, 'email' => 'test@example.com'];

        RuleEvaluationService::evaluateCart($lineItem, $customer);
    }

    public function test_evaluate_cart_throws_exception_when_negative_quantity()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('quantity must be positive and unitPrice must be non-negative.');

        $lineItem = [
            'productId' => 1,
            'quantity' => -1,
            'unitPrice' => 50.00
        ];
        $customer = ['id' => 1, 'email' => 'test@example.com'];

        RuleEvaluationService::evaluateCart($lineItem, $customer);
    }

    public function test_evaluate_cart_throws_exception_when_negative_price()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('quantity must be positive and unitPrice must be non-negative.');

        $lineItem = [
            'productId' => 1,
            'quantity' => 2,
            'unitPrice' => -10.00
        ];
        $customer = ['id' => 1, 'email' => 'test@example.com'];

        RuleEvaluationService::evaluateCart($lineItem, $customer);
    }

    public function test_evaluate_cart_returns_empty_result_when_no_active_rules()
    {
        $lineItem = [
            'productId' => 1,
            'quantity' => 2,
            'unitPrice' => 50.00
        ];
        $customer = ['id' => 1, 'email' => 'test@example.com'];

        $result = RuleEvaluationService::evaluateCart($lineItem, $customer);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('applied', $result);
        $this->assertArrayHasKey('totalDiscount', $result);
        $this->assertArrayHasKey('finalLineTotal', $result);
        $this->assertArrayHasKey('originalLineTotal', $result);
        $this->assertEmpty($result['applied']);
        $this->assertEquals(100.00, $result['finalLineTotal']);
        $this->assertEquals(100.00, $result['originalLineTotal']);
        $this->assertEquals(0.00, $result['totalDiscount']);
    }

    public function test_evaluate_cart_with_valid_data_structure()
    {
        $lineItem = [
            'productId' => 1,
            'quantity' => 2,
            'unitPrice' => 50.00
        ];
        $customer = ['id' => 1, 'email' => 'test@example.com'];

        $result = RuleEvaluationService::evaluateCart($lineItem, $customer);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('applied', $result);
        $this->assertArrayHasKey('totalDiscount', $result);
        $this->assertArrayHasKey('finalLineTotal', $result);
        $this->assertArrayHasKey('originalLineTotal', $result);
        $this->assertEquals(100.00, $result['finalLineTotal']);
        $this->assertEquals(100.00, $result['originalLineTotal']);
    }

    public function test_prepare_facts_structure()
    {
        $lineItem = [
            'productId' => 1,
            'quantity' => 2,
            'unitPrice' => 50.00
        ];
        $customer = ['id' => 1, 'email' => 'test@example.com'];

        $reflection = new ReflectionClass(RuleEvaluationService::class);
        $method = $reflection->getMethod('prepareFacts');
        $method->setAccessible(true);

        $facts = $method->invoke(null, $lineItem, $customer);

        $this->assertArrayHasKey('customer', $facts);
        $this->assertArrayHasKey('line', $facts);
        $this->assertEquals(1, $facts['customer']['id']);
        $this->assertEquals('test@example.com', $facts['customer']['email']);
        $this->assertEquals(1, $facts['line']['productId']);
        $this->assertEquals(2, $facts['line']['quantity']);
        $this->assertEquals(50.00, $facts['line']['unitPrice']);
    }

    public function test_extract_email_domain()
    {
        $reflection = new ReflectionClass(RuleEvaluationService::class);
        $method = $reflection->getMethod('extractEmailDomain');
        $method->setAccessible(true);

        $domain = $method->invoke(null, 'user@example.com');
        $this->assertEquals('example.com', $domain);

        $domain = $method->invoke(null, 'test@gmail.com');
        $this->assertEquals('gmail.com', $domain);

        $domain = $method->invoke(null, 'invalid-email');
        $this->assertEquals('', $domain);
    }

    public function test_format_response_structure()
    {
        $appliedRules = [
            ['id' => 1, 'name' => 'Test Rule']
        ];
        $lineItem = [
            'productId' => 1,
            'quantity' => 2,
            'unitPrice' => 50.00
        ];
        $totalDiscount = 10.00;

        $reflection = new ReflectionClass(RuleEvaluationService::class);
        $method = $reflection->getMethod('formatResponse');
        $method->setAccessible(true);

        $response = $method->invoke(null, $appliedRules, $lineItem, $totalDiscount);

        $this->assertArrayHasKey('applied', $response);
        $this->assertArrayHasKey('totalDiscount', $response);
        $this->assertArrayHasKey('finalLineTotal', $response);
        $this->assertArrayHasKey('originalLineTotal', $response);
        $this->assertEquals($appliedRules, $response['applied']);
        $this->assertEquals(90.00, $response['finalLineTotal']);
        $this->assertEquals(100.00, $response['originalLineTotal']);
        $this->assertEquals(10.00, $response['totalDiscount']);
    }

    public function test_format_response_caps_discount_at_total()
    {
        $appliedRules = [
            ['id' => 1, 'name' => 'Test Rule']
        ];
        $lineItem = [
            'productId' => 1,
            'quantity' => 2,
            'unitPrice' => 50.00
        ];
        $totalDiscount = 150.00; 

        $reflection = new ReflectionClass(RuleEvaluationService::class);
        $method = $reflection->getMethod('formatResponse');
        $method->setAccessible(true);

        $response = $method->invoke(null, $appliedRules, $lineItem, $totalDiscount);

        $this->assertEquals(0.00, $response['finalLineTotal']);
        $this->assertGreaterThanOrEqual(0, $response['finalLineTotal']);
    }
}
