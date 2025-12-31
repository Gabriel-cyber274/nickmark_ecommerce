<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAnswer extends Model
{
    //
    protected $fillable = ['product_id', 'category_question_id', 'answer'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function question()
    {
        return $this->belongsTo(CategoryQuestion::class, 'category_question_id');
    }
}
