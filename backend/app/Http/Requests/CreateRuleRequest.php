<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRuleRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'salience' => 'nullable|integer|min:1|max:100',
            'stackable' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'conditions' => 'required|array|min:1',
            'conditions.*.field' => 'required|string',
            'conditions.*.operator' => 'required|string',
            'conditions.*.value' => 'required',
            'actions' => 'required|array|min:1',
            'actions.*.type' => 'required|string|in:percent,fixed,free_units,tiered_percent',
            'actions.*.value' => 'required|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Rule name is required!',
            'conditions.required' => 'At least one condition is required!',
            'actions.required' => 'At least one action is required!',
            'valid_until.after' => 'Valid until date must be after valid from date!',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Rule Name',
            'description' => 'Description',
            'salience' => 'Priority',
            'stackable' => 'Stackable',
            'is_active' => 'Active Status',
            'conditions' => 'Conditions',
            'actions' => 'Actions',
            'valid_from' => 'Valid From',
            'valid_until' => 'Valid Until',
        ];
    }
}