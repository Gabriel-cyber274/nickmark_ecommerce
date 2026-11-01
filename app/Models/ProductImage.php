<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'image_url'];

    protected $appends = ['image_url'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute(): string
    {
        $path = $this->attributes['image_url'] ?? '';
        
        if (empty($path)) {
            return '';
        }
        
        // If it's already a full URL, return as-is
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }
        
        // Convert local path to full URL
        return Storage::disk('public')->url($path);
    }
}
