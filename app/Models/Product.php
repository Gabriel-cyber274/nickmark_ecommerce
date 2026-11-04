<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Mail\NewProductNotification;
use Illuminate\Support\Facades\Mail;

class Product extends Model
{
    //
    protected $fillable = ['category_id', 'name', 'slug', 'price', 'previous_price', 'description', 'views', 'is_new', 'is_featured', 'brand_name'];

    protected $casts = [
        'is_new' => 'boolean',
        'is_featured' => 'boolean',
        'previous_price' => 'float',
        'price' => 'float',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Generate slug on creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

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

        static::created(function (Product $product) {
            // Send new product notification to all users via queue
            // Using chunk to avoid memory issues with large user bases
            \App\Models\User::chunkById(100, function ($users) use ($product) {
                foreach ($users as $user) {
                    Mail::to($user->email)->queue(new NewProductNotification($product));
                }
            });
        });
    }

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

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
