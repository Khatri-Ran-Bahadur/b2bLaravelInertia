<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Relationships

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_users')
            ->withPivot(['role', 'status'])
            ->withTimestamps();
    }

    public function owner(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_users')
            ->wherePivot('role', 'owner')
            ->withPivot(['role', 'status'])
            ->withTimestamps()
            ->limit(1);
    }

    public function complains(): HasMany
    {
        return $this->hasMany(CompanyComplain::class, 'company_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CompanyDocument::class);
    }

    public function tenders(): HasMany
    {
        return $this->hasMany(Tender::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }



    public function reviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class);
    }


    /**
     * Get the products for the company.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'company_categories');
    }
}
