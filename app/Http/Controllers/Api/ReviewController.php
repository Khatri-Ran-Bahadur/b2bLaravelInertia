<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ReviewAnswerRequest;
use App\Models\Review;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\StoreReviewRequest;
use App\Models\ReviewAnswer;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends ApiController
{
    /**
     * Add a review for a product.
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Create the review
        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'review' => $validated['review'],
            'rating' => $validated['rating'],
            'is_approved' => false, // Default to false
        ]);

        // Add images if any
        if ($request->has('images')) {
            $review->addImages($request->file('images'));
        }

        return $this->detailRespond(new ReviewResource($review));
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review): JsonResponse
    {
        if ($review->product->user_id !== Auth::id()) {
            return $this->respondWithError(__("Unauthorized"));
        }
        $review->delete();
        return $this->respondSuccess(__("Review deleted successfully"));
    }

    /**
     * Approve/Disapprove a review.
     */
    public function approve(Review $review): JsonResponse
    {
        if ($review->product->user_id !== Auth::id()) {
            return $this->respondWithError(__("Unauthorized"));
        }

        // Toggle approval status
        $review->is_approved = !$review->is_approved;
        $review->save();

        return  $this->detailRespond(new ReviewResource($review));
    }

    /**
     * List all reviews for products owned by the authenticated user.
     */
    public function myReviews(): JsonResponse
    {
        $perPage = request('perPage', 10);
        $reviews = Review::whereHas('product', function ($query) {
            $query->where('user_id', Auth::id());
        })->with('product', 'user')->paginate($perPage);

        return response()->json(ReviewResource::collection($reviews), 200);
    }

    public function reply(ReviewAnswerRequest $request, Review $review): JsonResponse
    {
        if ($review->product->user_id !== Auth::id()) {
            return $this->respondWithError(__("Unauthorized"));
        }

        ReviewAnswer::create([
            'user_id' => Auth::id(),
            'answer' => $request->answer,
            'review_id' => $review->id
        ]);

        return $this->detailRespond(new ReviewResource($review));
    }
}
