<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRuleRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'salience' => 'sometimes|integer|min:1|max:100',
            'stackable' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'conditions' => 'sometimes|array|min:1',
            'conditions.*.field' => 'required_with:conditions|string',
            'conditions.*.operator' => 'required_with:conditions|string',
            'conditions.*.value' => 'required_with:conditions',
            'actions' => 'sometimes|array|min:1',
            'actions.*.type' => 'required_with:actions|string|in:percent,fixed,free_units,tiered_percent',
            'actions.*.value' => 'required_with:actions|numeric|min:0',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Rule name must be a string!',
            'conditions.min' => 'At least one condition is required when updating conditions!',
            'actions.min' => 'At least one action is required when updating actions!',
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