<?php

namespace Database\Seeders;

use App\Models\CategoryQuestion;
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
                'image' => asset('/assets/images/demos/demo-4/cats/1.png'),
                'description' => 'All types of laptops including gaming, business and student laptops.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Mobile Phones',
                'image' => asset('/assets/Iphone.png'),
                'description' => 'Smartphones of all brands and models.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Tablets',
                'image' => asset('/assets/Tablet.png'),
                'description' => 'Tablets including Android and iOS devices.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Desktop Computers',
                'image' => asset('/assets/Surface-Studio.png'),
                'description' => 'Desktop PCs and system units.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Audio Devices',
                'image' => asset('/assets/images/demos/demo-4/products/product-10.jpg'),
                'description' => 'Speakers, headphones, earbuds and sound systems.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Computer Accessories',
                'image' => asset('/assets/MK-ft.png'),
                'description' => 'Keyboards, mouse, storage devices and cables.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Phone Accessories',
                'image' => asset('/assets/charger.png'),
                'description' => 'Chargers, power banks, cases and screen protectors.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Gaming Devices',
                'image' => asset('/assets/pad.png'),
                'description' => 'Gaming laptops, controllers and gaming accessories.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // [
            //     'name' => 'Networking Devices',
            //     'image' => 'categories/networking.png',
            //     'description' => 'Routers, modems and networking accessories.',
            //     'created_at' => $now,
            //     'updated_at' => $now,
            // ],
            // [
            //     'name' => 'Printers & Scanners',
            //     'image' => asset('/assets/printer.png'),
            //     'description' => 'Printers, scanners and office printing equipment.',
            //     'created_at' => $now,
            //     'updated_at' => $now,
            // ],
            [
                'name' => 'Office Equipment',
                'image' => asset('/assets/printer.png'),
                'description' => 'Office machines and productivity equipment.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $categories = \App\Models\Category::all()->keyBy('name');

        $questions = [
            'Laptops' => [
                ['question' => 'Graphics Card', 'type' => 'select', 'is_required' => true],
                ['question' => 'RAM Size', 'type' => 'select', 'is_required' => true],
                ['question' => 'Screen Size', 'type' => 'select', 'is_required' => true],
                ['question' => 'Processor', 'type' => 'text', 'is_required' => false],
            ],
            'Mobile Phones' => [
                ['question' => 'Storage Capacity', 'type' => 'select', 'is_required' => true],
                ['question' => 'Camera Megapixels', 'type' => 'select', 'is_required' => true],
                ['question' => 'Battery Capacity', 'type' => 'select', 'is_required' => false],
                ['question' => '5G Support', 'type' => 'select', 'is_required' => false],
            ],
            'Tablets' => [
                ['question' => 'Screen Size', 'type' => 'select', 'is_required' => true],
                ['question' => 'Storage Capacity', 'type' => 'select', 'is_required' => true],
                ['question' => 'Cellular Connectivity', 'type' => 'select', 'is_required' => false],
                ['question' => 'Stylus Support', 'type' => 'select', 'is_required' => false],
            ],
            'Desktop Computers' => [
                ['question' => 'Graphics Card', 'type' => 'select', 'is_required' => true],
                ['question' => 'Power Supply', 'type' => 'select', 'is_required' => true],
                ['question' => 'Processor', 'type' => 'text', 'is_required' => false],
                ['question' => 'RAM Size', 'type' => 'select', 'is_required' => false],
            ],
            'Audio Devices' => [
                ['question' => 'Noise Cancellation', 'type' => 'select', 'is_required' => true],
                ['question' => 'Battery Life', 'type' => 'select', 'is_required' => true],
                ['question' => 'Connection Type', 'type' => 'select', 'is_required' => true],
                ['question' => 'Waterproof Rating', 'type' => 'text', 'is_required' => false],
            ],
            'Computer Accessories' => [
                ['question' => 'Connection Type', 'type' => 'select', 'is_required' => true],
                ['question' => 'Compatibility', 'type' => 'text', 'is_required' => true],
                ['question' => 'Ergonomics', 'type' => 'select', 'is_required' => false],
                ['question' => 'Backlighting', 'type' => 'select', 'is_required' => false],
            ],
            'Phone Accessories' => [
                ['question' => 'Compatibility', 'type' => 'select', 'is_required' => true],
                ['question' => 'Wireless Charging', 'type' => 'select', 'is_required' => true],
                ['question' => 'Waterproof Rating', 'type' => 'text', 'is_required' => false],
                ['question' => 'MagSafe Support', 'type' => 'select', 'is_required' => false],
            ],
            'Gaming Devices' => [
                ['question' => 'Storage Capacity', 'type' => 'select', 'is_required' => true],
                ['question' => 'Controller Type', 'type' => 'select', 'is_required' => true],
                ['question' => 'Resolution', 'type' => 'select', 'is_required' => true],
                ['question' => 'Compatibility', 'type' => 'text', 'is_required' => false],
            ],
            'Office Equipment' => [
                ['question' => 'Print Speed', 'type' => 'select', 'is_required' => true],
                ['question' => 'Paper Capacity', 'type' => 'select', 'is_required' => true],
                ['question' => 'Connection Type', 'type' => 'select', 'is_required' => true],
                ['question' => 'Color Printing', 'type' => 'select', 'is_required' => false],
            ],
        ];

        foreach ($questions as $categoryName => $categoryQuestions) {
            $category = $categories[$categoryName];
            foreach ($categoryQuestions as $catQuestion) {
                CategoryQuestion::create([
                    'category_id' => $category->id,
                    'question' => $catQuestion['question'],
                    'type' => $catQuestion['type'],
                    'is_required' => $catQuestion['is_required'],
                ]);
            }
        }
    }
}
