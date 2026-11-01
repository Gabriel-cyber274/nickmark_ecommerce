<?php
require 'vendor/autoload.php';
$app = new Illuminate\Foundation\Application('local');
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo 'View compiled: ' . config('view.compiled') . PHP_EOL;
echo 'Cache default: ' . config('cache.default') . PHP_EOL;