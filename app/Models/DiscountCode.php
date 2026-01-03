<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    protected $fillable = [
        'code',
        'min_amount',
        'discount_amount',
        'expires_at',
    ];

    protected $dates = [
        'expires_at',
    ];
}
