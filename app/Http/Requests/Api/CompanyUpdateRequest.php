<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
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
            'tin_number' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'okpo' => 'nullable|string|max:255',
            'search_tags' => 'nullable',
            'description' => 'nullable',
            'category_ids' => 'nullable|array',
        ];
    }
}
