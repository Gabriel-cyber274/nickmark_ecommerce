<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate(
            ['id' => 1],
            [
                'dispatch_return' => 'We offer a 7-day return policy for all items in original condition with tags attached. Please contact our customer service at the phone numbers below to initiate a return. Refunds will be processed within 5-7 business days after we receive the returned item.',
                'account_number' => '5054060087',
                'bank_name' => 'Moniepoint',
                'account_name' => 'Nick mark Technologies',
                'phone_number1' => '08062965548',
                'phone_number2' => '07044043049',
                'phone_number3' => '09027868088',
                'whatsapp_number' => '2347018521912',
                'is_paystack_enabled' => true,
                'is_whatsapp_enabled' => false,
            ]
        );
    }
}