<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuleController;
use App\Http\Controllers\EvaluateController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CategoryController;


Route::group(["prefix" => "v1"], function () {
    // Rule CRUD endpoint
    Route::apiResource('rules', RuleController::class);

    // Rule evaluation endpoint
    Route::post('/evaluate', [EvaluateController::class, 'evaluate']);

    // Product endpoints
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // Customer endpoints
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);

    // Category endpoints
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
});
