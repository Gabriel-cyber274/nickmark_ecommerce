<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo 'Settings count: ' . \App\Models\Setting::count() . PHP_EOL;
$s = \App\Models\Setting::latest()->first();
if($s) {
    echo 'is_paystack_enabled: ' . ($s->is_paystack_enabled ? 'true' : 'false') . PHP_EOL;
    echo 'is_whatsapp_enabled: ' . ($s->is_whatsapp_enabled ? 'true' : 'false') . PHP_EOL;
    echo 'account_name: ' . $s->account_name . PHP_EOL;
    echo 'account_number: ' . $s->account_number . PHP_EOL;
    echo 'bank_name: ' . $s->bank_name . PHP_EOL;
} else {
    echo 'No settings found' . PHP_EOL;
}