<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
 protected $fillable = [
        'dispatch_return',
        'account_number',
        'bank_name',
        'account_name',
        'is_paystack_enabled',
        'is_whatsapp_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_paystack_enabled' => 'boolean',
            'is_whatsapp_enabled' => 'boolean',
        ];
    }
}
