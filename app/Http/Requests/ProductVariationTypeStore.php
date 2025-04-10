<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariationTypeStore extends FormRequest
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
            'variationTypes' => 'required|array',
            'variationTypes.*.name' => 'required|string',
            'variationTypes.*.type' => 'required|string|in:select,radio,checkbox',
            'variationTypes.*.options' => 'nullable|array',
            'variationTypes.*.options.*.name' => 'required|string',
            'variationTypes.*.options.*.images.*' => 'nullable|image|max:2048',
        ];
    }
}
