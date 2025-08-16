<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'template_id' => ['required', 'exists:contract_templates,id'],
            'status' => ['sometimes', 'string', 'in:draft,active,expired,cancelled'],
            'expires_at' => ['nullable', 'date', 'after:today'],
            'total_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'variables' => ['sometimes', 'array'],
            'variables.*.name' => ['required_with:variables', 'string'],
            'variables.*.value' => ['required_with:variables', 'string'],
            'parties' => ['sometimes', 'array'],
            'parties.*.name' => ['required_with:parties', 'string', 'max:255'],
            'parties.*.type' => ['required_with:parties', 'string', 'max:100'],
            'parties.*.email' => ['nullable', 'email', 'max:255'],
            'parties.*.phone' => ['nullable', 'string', 'max:20'],
            'parties.*.address' => ['nullable', 'string', 'max:500'],
            'parties.*.city' => ['nullable', 'string', 'max:100'],
            'parties.*.state' => ['nullable', 'string', 'max:100'],
            'parties.*.zip_code' => ['nullable', 'string', 'max:20'],
            'parties.*.country' => ['nullable', 'string', 'max:100'],
            'parties.*.tax_id' => ['nullable', 'string', 'max:100'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Contract title is required.',
            'title.max' => 'Contract title cannot exceed 255 characters.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'template_id.required' => 'Template selection is required.',
            'template_id.exists' => 'Selected template does not exist.',
            'status.in' => 'Status must be one of: draft, active, expired, cancelled.',
            'expires_at.after' => 'Expiration date must be in the future.',
            'total_value.numeric' => 'Total value must be a number.',
            'total_value.min' => 'Total value cannot be negative.',
            'currency.size' => 'Currency must be exactly 3 characters.',
            'variables.*.name.required_with' => 'Variable name is required when providing variables.',
            'variables.*.value.required_with' => 'Variable value is required when providing variables.',
            'parties.*.name.required_with' => 'Party name is required when providing parties.',
            'parties.*.type.required_with' => 'Party type is required when providing parties.',
            'parties.*.email.email' => 'Party email must be a valid email address.',
            'parties.*.name.max' => 'Party name cannot exceed 255 characters.',
            'parties.*.type.max' => 'Party type cannot exceed 100 characters.',
            'parties.*.email.max' => 'Party email cannot exceed 255 characters.',
            'parties.*.phone.max' => 'Party phone cannot exceed 20 characters.',
            'parties.*.address.max' => 'Party address cannot exceed 500 characters.',
            'parties.*.city.max' => 'Party city cannot exceed 100 characters.',
            'parties.*.state.max' => 'Party state cannot exceed 100 characters.',
            'parties.*.zip_code.max' => 'Party ZIP code cannot exceed 20 characters.',
            'parties.*.country.max' => 'Party country cannot exceed 100 characters.',
            'parties.*.tax_id.max' => 'Party tax ID cannot exceed 100 characters.',
        ];
    }
}
