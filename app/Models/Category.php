<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'image'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function questions()
    {
        return $this->hasMany(CategoryQuestion::class);
    }



    protected static function booted(): void
    {
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
