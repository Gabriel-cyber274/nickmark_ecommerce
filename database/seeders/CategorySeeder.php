<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('categories')->insert([
            [
                'name' => 'Laptops',
                'image' => 'categories/laptops.png',
                'description' => 'All types of laptops including gaming, business and student laptops.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Mobile Phones',
                'image' => 'categories/mobile-phones.png',
                'description' => 'Smartphones of all brands and models.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Tablets',
                'image' => 'categories/tablets.png',
                'description' => 'Tablets including Android and iOS devices.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Desktop Computers',
                'image' => 'categories/desktops.png',
                'description' => 'Desktop PCs and system units.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Audio Devices',
                'image' => 'categories/audio.png',
                'description' => 'Speakers, headphones, earbuds and sound systems.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Computer Accessories',
                'image' => 'categories/computer-accessories.png',
                'description' => 'Keyboards, mouse, storage devices and cables.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Phone Accessories',
                'image' => 'categories/phone-accessories.png',
                'description' => 'Chargers, power banks, cases and screen protectors.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Gaming Devices',
                'image' => 'categories/gaming.png',
                'description' => 'Gaming laptops, controllers and gaming accessories.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Networking Devices',
                'image' => 'categories/networking.png',
                'description' => 'Routers, modems and networking accessories.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Printers & Scanners',
                'image' => 'categories/printers.png',
                'description' => 'Printers, scanners and office printing equipment.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Office Equipment',
                'image' => 'categories/office-equipment.png',
                'description' => 'Office machines and productivity equipment.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
