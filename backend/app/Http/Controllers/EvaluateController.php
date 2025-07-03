<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluateRuleRequest;
use App\Services\RuleEvaluationService;
use App\Traits\ResponseTrait;

class EvaluateController extends Controller
{
    use ResponseTrait;

    public function evaluate(EvaluateRuleRequest $request)
    {
        try {
            $validated = $request->validated();
            
            $result = RuleEvaluationService::evaluateCart(
                $validated['line'],
                $validated['customer'],
                $validated['orderReference'] ?? null
            );
            
            return $this->successResponse($result, "Rules evaluated successfully", 200);
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to evaluate rules: " . $e->getMessage(), 500);
        }
    }
}