<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRuleRequest;
use App\Http\Requests\UpdateRuleRequest;
use App\Services\RuleService;
use App\Traits\ResponseTrait;

class RuleController extends Controller
{
    use ResponseTrait;

    public function index()
    {
        try {
            $page = request('page', 0);
            $rules = RuleService::getAllRules($page);
            return $this->successResponse($rules, "Rules fetched successfully", 200);
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to fetch rules: " . $e->getMessage(), 500);
        }
    }

    public function store(CreateRuleRequest $request)
    {
        try {
            $rule = RuleService::createRule($request->validated());
            return $this->successResponse($rule, "Rule created successfully", 201);
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to create rule: " . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            if (!$id) {
                return $this->errorResponse("Rule ID not provided", 400);
            }
            $rule = RuleService::getSingleRule($id);
            return $this->successResponse($rule, "Rule fetched successfully", 200);
        } catch (\Exception $e) {
            return $this->errorResponse("Failed to fetch rule: " . $e->getMessage(), 500);
        }
    }

    
}