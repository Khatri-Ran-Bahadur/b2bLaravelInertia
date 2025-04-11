<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Tender extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'budget_from',
        'budget_to',
        'phone',
        'email',
        'status',
        'company_id',
        'tender_category_id',
    ];

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width('100');
        $this->addMediaConversion('small')->width('480');
        $this->addMediaConversion('large')->width('1200');
    }


    /**
     * Get the company that owns the tender.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the category that owns the tender.
     */
    public function tenderCategory(): BelongsTo
    {
        return $this->belongsTo(TenderCategory::class);
    }

    public function tenderProducts(): HasMany
    {
        return $this->hasMany(TenderProduct::class);
    }

    /**
     * Register media collections for the model.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('tender_images');
    }
}
