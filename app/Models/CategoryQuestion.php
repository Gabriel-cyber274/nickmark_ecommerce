<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryQuestion extends Model
{
    //
    protected $fillable = ['category_id', 'question', 'type', 'is_required'];

    protected $casts = [
        'is_required' => 'boolean',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function answers()
    {
        return $this->hasMany(ProductAnswer::class);
    }
}
