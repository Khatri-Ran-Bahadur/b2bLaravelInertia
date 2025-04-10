<?php

namespace App\Models;

use App\Enums\ProductStatusEnum;
use App\Enums\VendorStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, HasSlug;

    protected $guarded = [];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }


    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')->width('100');
        $this->addMediaConversion('small')->width('480');
        $this->addMediaConversion('large')->width('1200');
    }

    public function scopeForVendor(Builder $query)
    {
        return $query->where('created_by', auth()->user()->id);
    }

    public function scopePublished(Builder $query)
    {
        return $query->where('products.status', ProductStatusEnum::Published);
    }


    public function scopeForWebsite(Builder $query)
    {
        return $query->published()->vendorApproved();
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variationTypes(): HasMany
    {
        return $this->hasMany(VariationType::class);
    }

    public function options(): HasManyThrough
    {
        return $this->hasManyThrough(VariationTypeOption::class, VariationType::class, 'product_id', 'variation_type_id', 'id', 'id');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class);
    }


    public function getPriceForOptions($optionIds = [])
    {
        $optionIds = array_values($optionIds);
        sort($optionIds);

        foreach ($this->variations as $variation) {
            $a = $variation->variation_type_option_ids;
            sort($a);
            if ($optionIds == $a) {
                return $variation->price != null ? $variation->price : $this->price;
            }
        }

        return $this->price;
    }

    public function getImageForOptions(array $optionIds = null)
    {
        if ($optionIds) {
            $optionIds = array_values($optionIds);
            sort($optionIds);
            $options = VariationTypeOption::whereIn('id', $optionIds)->get();
            foreach ($options as $option) {
                $image = $option->getFirstMediaUrl('images', 'small');
                if ($image) {
                    return $image;
                }
            }
        }
        return $this->getFirstMediaUrl('images', 'small') ?: asset('images/placeholder.png');
    }


    public function getImagesForOptions(array $optionIds = null)
    {
        if ($optionIds) {
            $optionIds = array_values($optionIds);
            sort($optionIds);
            $options = VariationTypeOption::whereIn('id', $optionIds)->get();
            foreach ($options as $option) {
                $images = $option->getMedia('images');
                if ($images) {
                    return $images;
                }
            }
        }
        return $this->getMedia('images');
    }

    public function getFirstImageUrl($collectionName = 'images', $conversion = 'small'): string
    {
        if ($this->options->count() > 0) {
            foreach ($this->options as $option) {
                $imageUrl = $option->getFirstMediaUrl($collectionName, $conversion);
                if ($imageUrl) {
                    return $imageUrl;
                }
            }
        }
        return $this->getFirstMediaUrl($collectionName, $conversion);
    }


    public function getImages(): MediaCollection
    {
        if ($this->options->count() > 0) {
            foreach ($this->options as $option) {
                $images = $option->getMedia('images');
                if ($images) {
                    return $images;
                }
            }
            return $images;
        }
        return $this->getMedia('images');
    }

    public function getPriceForFirstOptions(): float
    {
        $firstOptions = $this->getFirstOptionsMap();
        if ($firstOptions) {
            return $this->getPriceForOptions($firstOptions);
        }
        return $this->price;
    }

    public function getFirstOptionsMap(): array
    {
        return $this->variationTypes->mapWithKeys(fn($type) => [$type->id => $type->options[0]?->id])->toArray();
    }
}
