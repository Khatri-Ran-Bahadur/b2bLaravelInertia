<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
            'name' => 'required|string|max:2000',
            'brand' => 'nullable|string|max:2000',
            'description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|string|in:draft,published,archived',
            'quantity' => 'nullable|numeric|min:0',
            'is_available' => 'boolean',
            'dimention' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:255',
            'country_of_origin' => 'nullable|string|max:255',
            'search_keywords' => 'nullable|string|max:255',
            'search_keywords_2' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
