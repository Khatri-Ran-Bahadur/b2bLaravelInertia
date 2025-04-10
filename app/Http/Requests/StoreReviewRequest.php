<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust as per your authorization logic
    }

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'review' => 'required|string|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'images' => 'array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',  // Max 5 images, each up to 2MB
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'The selected product does not exist.',
            'review.required' => 'Review content is required.',
            'rating.required' => 'Rating is required.',
            'rating.min' => 'Rating must be at least 1.',
            'rating.max' => 'Rating must not exceed 5.',
            'images.*.image' => 'Each image must be an image file.',
            'images.*.mimes' => 'Each image must be of type jpeg, png, jpg, or gif.',
            'images.*.max' => 'Each image must not exceed 2MB.',
        ];
    }
}
