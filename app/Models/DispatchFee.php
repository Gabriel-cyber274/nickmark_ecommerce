<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchFee extends Model
{
    protected $fillable = [
        'state_id',
        'city_id',
        'amount',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
