<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    //
    protected $fillable = ['category_id', 'name', 'price', 'previous_price', 'description', 'views', 'is_new', 'is_featured', 'brand_name'];

    protected $casts = [
        'is_new' => 'boolean',
        'is_featured' => 'boolean',
        'previous_price' => 'float',
        'price' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function answers()
    {
        return $this->hasMany(ProductAnswer::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }


    /* =======================
     *  MODEL EVENTS
     * ======================= */
    protected static function booted(): void
    {
        static::deleting(function (Product $product) {
            foreach ($product->images as $image) {
                if (! $image->image_url) {
                    continue;
                }

                // Convert full URL → storage path
                $path = str_replace(
                    Storage::disk('public')->url(''),
                    '',
                    $image->image_url
                );

                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            // Delete image records
            $product->images()->delete();
        });
    }
}
