<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'slug' => 'required|string|max:2000|unique:products,slug',
            'description' => 'nullable|string',
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|between:0,999999.9999',
            'status' => 'required|string|in:active,inactive',
            'quantity' => 'nullable|string',
            'created_by' => 'required|exists:users,id',
            'updated_by' => 'required|exists:users,id',
        ];
    }
}
