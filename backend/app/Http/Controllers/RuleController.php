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

    
}