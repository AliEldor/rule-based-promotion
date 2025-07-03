<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluateRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'line' => 'required|array',
            'line.productId' => 'required|integer|min:1',
            'line.quantity' => 'required|integer|min:1',
            'line.unitPrice' => 'required|numeric|min:0',
            'line.categoryId' => 'nullable|integer|min:1',
            
            'customer' => 'required|array',
            'customer.id' => 'nullable|integer|min:1',
            'customer.email' => 'nullable|email|max:150',
            'customer.type' => 'nullable|string|max:50',
            'customer.loyaltyTier' => 'nullable|string|max:20',
            'customer.ordersCount' => 'nullable|integer|min:0',
            'customer.city' => 'nullable|string|max:100',
            
            'orderReference' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'line.required' => 'Line item data is required!',
            'line.productId.required' => 'Product ID is required!',
            'line.quantity.required' => 'Quantity is required!',
            'line.unitPrice.required' => 'Unit price is required!',
            'customer.required' => 'Customer data is required!',
        ];
    }

    public function attributes(): array
    {
        return [
            'line.productId' => 'Product ID',
            'line.quantity' => 'Quantity',
            'line.unitPrice' => 'Unit Price',
            'line.categoryId' => 'Category ID',
            'customer.id' => 'Customer ID',
            'customer.email' => 'Customer Email',
            'customer.type' => 'Customer Type',
            'customer.loyaltyTier' => 'Loyalty Tier',
            'customer.ordersCount' => 'Orders Count',
            'customer.city' => 'City',
            'orderReference' => 'Order Reference',
        ];
    }
}