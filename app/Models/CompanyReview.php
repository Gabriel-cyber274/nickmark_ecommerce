<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyReview extends Model
{
    // Specify which fields can be mass-assigned
    protected $fillable = [
        'name',
        'review',
        'relationship',
    ];
}
