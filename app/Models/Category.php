<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'image'];

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
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function (Category $category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        // When force deleting (permanent)
        static::forceDeleted(function ($category) {
            self::deleteImageFromStorage($category);
        });

        // OPTIONAL: If you want image deleted on normal delete too
        static::deleted(function ($category) {
            if (! $category->isForceDeleting()) {
                self::deleteImageFromStorage($category);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function questions()
    {
        return $this->hasMany(CategoryQuestion::class);
    }

    protected static function deleteImageFromStorage(self $category): void
    {
        if (! $category->image) {
            return;
        }

        $path = str_replace(
            Storage::disk('public')->url(''),
            '',
            $category->image
        );

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
