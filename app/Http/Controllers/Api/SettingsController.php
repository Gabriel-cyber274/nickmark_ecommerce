<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $setting = Setting::latest()->first();

        if (!$setting) {
            return response()->json([
                'dispatch_return' => '',
                'is_paystack_enabled' => false,
                'is_whatsapp_enabled' => false,
                'account_name' => '',
                'account_number' => '',
                'bank_name' => '',
                'phone_number1' => '',
                'phone_number2' => '',
                'phone_number3' => '',
                'whatsapp_number' => '',
            ]);
        }

        return response()->json($setting->only([
            'dispatch_return',
            'is_paystack_enabled',
            'is_whatsapp_enabled',
            'account_name',
            'account_number',
            'bank_name',
            'phone_number1',
            'phone_number2',
            'phone_number3',
            'whatsapp_number',
        ]));
    }
}