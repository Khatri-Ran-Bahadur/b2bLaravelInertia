<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tender extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'company_id',
        'tender_category_id',
    ];

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
}
