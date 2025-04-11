<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenderProduct extends Model
{
    protected $fillable = [
        'tender_id',
        'product_name',
        'quantity',
        'unit',
        'status',
    ];


    /**
     * Get the tender that owns the tender product.
     */
    public function tender(): BelongsTo
    {
        return $this->belongsTo(Tender::class);
    }
}
