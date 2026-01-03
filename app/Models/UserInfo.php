<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'state_id',
        'city_id',
        'address',
        'postal_code',
    ];

    /**
     * A user info belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A user info belongs to a state
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * A user info belongs to a city
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
