<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\EvaluateController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Rule CRUD endpoint
Route::apiResource('rules', RuleController::class);

// Rule evaluation endpoint
Route::post('/evaluate', [EvaluateController::class, 'evaluate']);