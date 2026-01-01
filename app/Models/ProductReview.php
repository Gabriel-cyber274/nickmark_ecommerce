<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'name',
        'rate',
        'comment',
    ];

    /**
     * A review belongs to a product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * A review may belong to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
