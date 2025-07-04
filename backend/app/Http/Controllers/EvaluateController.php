<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluateRuleRequest;
use App\Services\RuleEvaluationService;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;

class EvaluateController extends Controller
{
    use ResponseTrait;

    public function evaluate(EvaluateRuleRequest $request)
    {
        try {
            Log::info('Incoming request data:', [
                'all' => $request->all(),
                'headers' => $request->headers->all(),
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);

            $validated = $request->validated();

            Log::info('Validated data:', $validated);

            $result = RuleEvaluationService::evaluateCart(
                $validated['line'],
                $validated['customer'],
                $validated['orderReference'] ?? null
            );

            Log::info('Evaluation result:', $result);

            return $this->successResponse($result, "Rules evaluated successfully", 200);
        } catch (\Exception $e) {
            Log::error('Error evaluating rules:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse("Failed to evaluate rules: " . $e->getMessage(), 500);
        }
    }
}
