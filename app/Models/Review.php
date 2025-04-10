<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Review extends Model implements HasMedia
{
    use InteractsWithMedia;

    // Guarded attributes
    protected $guarded = ['id'];

    /**
     * Get the product that owns the review.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user that owns the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all media associated with the review.
     */
    public function images(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'model');
    }

    /**
     * Attach media files to the review.
     */
    public function addImages($files)
    {
        foreach ($files as $file) {
            $this->addMedia($file)
                ->toMediaCollection('images');
        }
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ReviewAnswer::class, 'review_id');
    }
}
