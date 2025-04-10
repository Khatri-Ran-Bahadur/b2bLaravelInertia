<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'status',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the URL for the category.
     *
     * @return string
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the children of the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get all descendants (recursive children)
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (recursive parents)
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }


    /**
     * Scope a query to only include parent categories.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsParent($query)
    {
        return $query->whereNull('parent_id');
    }


    /**
     * Get the URL for the category.
     *
     * @return string
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_category');
    }




    /**
     * Get full hierarchy path
     */
    public function getPathAttribute()
    {
        if (!$this->parent) {
            return $this->name;
        }

        return $this->parent->path . ' > ' . $this->name;
    }

    /**
     * Check if category is a leaf (has no children)
     */
    public function getIsLeafAttribute()
    {
        return $this->children()->count() === 0;
    }
}
