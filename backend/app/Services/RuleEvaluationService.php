<?php

namespace App\Services;

use App\Models\RuleApplication;
use InvalidArgumentException;
use Illuminate\Support\Str;

class RuleEvaluationService
{
    public static function evaluateCart(array $lineItem, array $customer, ?string $orderReference = null): array
    {
        if (empty($lineItem['productId']) || empty($lineItem['quantity']) || empty($lineItem['unitPrice'])) {
            throw new InvalidArgumentException('lineItem must have productId, quantity, and unitPrice.');
        }

        if ($lineItem['quantity'] <= 0 || $lineItem['unitPrice'] < 0) {
            throw new InvalidArgumentException('quantity must be positive and unitPrice must be non-negative.');
        }

        $rules = RuleService::getActiveRules();

        if (empty($rules)) {
            return self::formatResponse([], $lineItem, 0);
        }

        $facts = self::prepareFacts($lineItem, $customer);

        // process rules through engine
        $appliedRules = [];
        $totalDiscount = 0;
        $currentLineTotal = $lineItem['quantity'] * $lineItem['unitPrice'];

        foreach ($rules as $rule) {
            $engineResult = RuleEngineService::evaluateRule($rule, $facts);

            if ($engineResult['matched']) {
                $discount = min($engineResult['discount'], $currentLineTotal);

                $appliedRules[] = [
                    'ruleId' => $rule['id'],
                    'ruleName' => $rule['name'],
                    'discount' => $discount
                ];

                $totalDiscount += $discount;
                $currentLineTotal -= $discount;

                self::logRuleApplication($rule['id'], $lineItem, $customer, $discount, $orderReference);

                if (!$rule['stackable']) {
                    break;
                }
            }
        }

        return self::formatResponse($appliedRules, $lineItem, $totalDiscount);
    }

    private static function prepareFacts(array $lineItem, array $customer): array
    {
        return [
            'line' => [
                'productId' => $lineItem['productId'],
                'quantity' => $lineItem['quantity'],
                'unitPrice' => $lineItem['unitPrice'],
                'categoryId' => $lineItem['categoryId'] ?? null,
                'total' => $lineItem['quantity'] * $lineItem['unitPrice']
            ],
            'customer' => [
                'id' => $customer['id'] ?? null,
                'email' => $customer['email'] ?? '',
                'type' => $customer['type'] ?? 'retail',
                'loyaltyTier' => $customer['loyaltyTier'] ?? 'none',
                'ordersCount' => $customer['ordersCount'] ?? 0,
                'city' => $customer['city'] ?? '',
                'emailDomain' => self::extractEmailDomain($customer['email'] ?? '')
            ]
        ];
    }

    private static function extractEmailDomain(string $email): string
    {
        return str_contains($email, '@') ? substr($email, strpos($email, '@') + 1) : '';
    }

    private static function logRuleApplication(int $ruleId, array $lineItem, array $customer, float $discount, ?string $orderReference): void
    {
        RuleApplication::create([
            'rule_id' => $ruleId,
            'customer_id' => $customer['id'] ?? null,
            'order_reference' => $orderReference ?? 'EVAL_' . Str::upper(Str::random(8)),
            'line_item_data' => $lineItem,
            'customer_data' => $customer,
            'discount_amount' => $discount
        ]);
    }

    private static function formatResponse(array $appliedRules, array $lineItem, float $totalDiscount): array
    {
        $originalTotal = $lineItem['quantity'] * $lineItem['unitPrice'];
        $finalLineTotal = max(0, $originalTotal - $totalDiscount);

        return [
            'applied' => $appliedRules,
            'totalDiscount' => round($totalDiscount, 2),
            'finalLineTotal' => round($finalLineTotal, 2),
            'originalLineTotal' => round($originalTotal, 2)
        ];
    }
}