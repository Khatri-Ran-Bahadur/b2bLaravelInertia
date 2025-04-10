<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;

class TenderCategory extends Model
{
    use HasSlug;

    protected $fillable = [
        'name',
        'slug',
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

    public function parent()
    {
        return $this->belongsTo(TenderCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TenderCategory::class, 'parent_id');
    }
}
