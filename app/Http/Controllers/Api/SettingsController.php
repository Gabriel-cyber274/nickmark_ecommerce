<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;

class SettingsController extends Controller
{
    public function index()
    {
        $keys = [
            'delivery_returns',
            'paystack_enabled',
            'whatsapp_enabled',
            'account_name',
            'account_number',
            'bank_name',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $value = GeneralSetting::getValue($key);
            $settings[$key] = $value ?? '';
        }

        return response()->json($settings);
    }
}