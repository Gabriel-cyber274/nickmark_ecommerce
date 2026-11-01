<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function scopeGetValue($query, string $key, string $default = '')
    {
        $setting = $query->where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public function scopeSetValue($query, string $key, $value): void
    {
        $setting = $query->where('key', $key)->first();

        if ($setting) {
            $setting->value = $value;
            $setting->save();
        } else {
            $query->create([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}