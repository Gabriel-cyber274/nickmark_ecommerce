<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name', 'capital'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function dispatchFee()
    {
        return $this->hasOne(DispatchFee::class);
    }
}
