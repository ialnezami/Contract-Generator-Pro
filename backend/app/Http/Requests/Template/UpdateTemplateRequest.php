<?php

namespace App\Http\Requests\Template;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplateRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'content' => ['sometimes', 'string'],
            'category' => ['sometimes', 'string', 'max:100'],
            'is_public' => ['boolean'],
            'is_active' => ['boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'variables_schema' => ['nullable', 'array'],
            'variables' => ['sometimes', 'array'],
            'variables.*.name' => ['required_with:variables', 'string', 'max:100'],
            'variables.*.type' => ['required_with:variables', 'string', 'in:text,number,date,select,textarea'],
            'variables.*.label' => ['required_with:variables', 'string', 'max:255'],
            'variables.*.required' => ['boolean'],
            'variables.*.default_value' => ['nullable', 'string'],
            'variables.*.options' => ['nullable', 'array'],
            'variables.*.description' => ['nullable', 'string', 'max:500'],
            'variables.*.order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Template name cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'category.max' => 'Category cannot exceed 100 characters.',
            'tags.*.max' => 'Each tag cannot exceed 50 characters.',
            'variables.*.name.required_with' => 'Variable name is required when providing variables.',
            'variables.*.name.max' => 'Variable name cannot exceed 100 characters.',
            'variables.*.type.required_with' => 'Variable type is required when providing variables.',
            'variables.*.type.in' => 'Variable type must be one of: text, number, date, select, textarea.',
            'variables.*.label.required_with' => 'Variable label is required when providing variables.',
            'variables.*.label.max' => 'Variable label cannot exceed 255 characters.',
            'variables.*.description.max' => 'Variable description cannot exceed 500 characters.',
            'variables.*.order.integer' => 'Variable order must be a number.',
            'variables.*.order.min' => 'Variable order cannot be negative.',
        ];
    }
}
