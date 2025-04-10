<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'size',
        'type',
        'file_path',
        'verified',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
