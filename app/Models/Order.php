<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'discount_id',
        'reference',
        'payment_method',
        'total',
        'name',
        'email',
        'phone',
        'state_id',
        'city_id',
        'address',
        'postal_code',
        'order_note',
        'status',
        'subtotal'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
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
