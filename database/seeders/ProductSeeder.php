<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAnswer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');

        $products = [
            // Laptops
            [
                'category' => 'Laptops',
                'name' => 'MacBook Pro 16" M3 Max',
                'description' => 'Apple MacBook Pro 16-inch with M3 Max chip, 36GB unified memory, 1TB SSD. Space Black finish. Perfect for professional video editing, 3D rendering, and software development.',
                'price' => 3499999.00,
                'previous_price' => 3799999.00,
                'brand_name' => 'Apple',
                'is_new' => true,
                'is_featured' => true,
            ],
            [
                'category' => 'Laptops',
                'name' => 'Dell XPS 15 9530',
                'description' => 'Dell XPS 15 with Intel Core i7-13700H, 32GB RAM, 1TB SSD, NVIDIA RTX 4070 8GB. 15.6" 3.5K OLED touch display. Premium ultraportable for creators.',
                'price' => 2899999.00,
                'previous_price' => 3199999.00,
                'brand_name' => 'Dell',
                'is_new' => true,
                'is_featured' => false,
            ],
            [
                'category' => 'Laptops',
                'name' => 'ASUS ROG Zephyrus G14',
                'description' => 'ASUS ROG Zephyrus G14 with AMD Ryzen 9 7940HS, 16GB RAM, 1TB SSD, NVIDIA RTX 4060. 14" QHD 165Hz display. Compact gaming powerhouse.',
                'price' => 1899999.00,
                'previous_price' => 2099999.00,
                'brand_name' => 'ASUS',
                'is_new' => false,
                'is_featured' => true,
            ],
            [
                'category' => 'Laptops',
                'name' => 'Lenovo ThinkPad X1 Carbon Gen 11',
                'description' => 'Lenovo ThinkPad X1 Carbon with Intel Core i7-1365U, 32GB RAM, 1TB SSD. 14" 2.8K OLED display. Legendary keyboard, military-grade durability.',
                'price' => 2499999.00,
                'previous_price' => 2799999.00,
                'brand_name' => 'Lenovo',
                'is_new' => true,
                'is_featured' => false,
            ],
            [
                'category' => 'Laptops',
                'name' => 'HP Spectre x360 14"',
                'description' => 'HP Spectre x360 2-in-1 with Intel Core Ultra 7 155H, 16GB RAM, 1TB SSD. 14" 2.8K OLED touch display. Versatile convertible with pen support.',
                'price' => 1999999.00,
                'previous_price' => 2199999.00,
                'brand_name' => 'HP',
                'is_new' => true,
                'is_featured' => false,
            ],

            // Mobile Phones
            [
                'category' => 'Mobile Phones',
                'name' => 'iPhone 15 Pro Max 256GB',
                'description' => 'Apple iPhone 15 Pro Max with A17 Pro chip, 256GB storage, 48MP main camera, 6.7" Super Retina XDR display. Natural Titanium finish.',
                'price' => 1899999.00,
                'previous_price' => 1999999.00,
                'brand_name' => 'Apple',
                'is_new' => true,
                'is_featured' => true,
            ],
            [
                'category' => 'Mobile Phones',
                'name' => 'Samsung Galaxy S24 Ultra 512GB',
                'description' => 'Samsung Galaxy S24 Ultra with Snapdragon 8 Gen 3, 512GB storage, 200MP camera, 6.8" Dynamic AMOLED 2X. Titanium Gray with S Pen.',
                'price' => 1799999.00,
                'previous_price' => 1899999.00,
                'brand_name' => 'Samsung',
                'is_new' => true,
                'is_featured' => true,
            ],
            [
                'category' => 'Mobile Phones',
                'name' => 'Google Pixel 8 Pro 256GB',
                'description' => 'Google Pixel 8 Pro with Tensor G3, 256GB storage, 50MP main camera, 6.7" Super Actua display. Obsidian color with 7 years of updates.',
                'price' => 1299999.00,
                'previous_price' => 1399999.00,
                'brand_name' => 'Google',
                'is_new' => false,
                'is_featured' => false,
            ],
            [
                'category' => 'Mobile Phones',
                'name' => 'OnePlus 12 512GB',
                'description' => 'OnePlus 12 with Snapdragon 8 Gen 3, 512GB storage, 50MP triple camera, 6.82" LTPO AMOLED. Flowy Emerald with 100W fast charging.',
                'price' => 1099999.00,
                'previous_price' => 1199999.00,
                'brand_name' => 'OnePlus',
                'is_new' => true,
                'is_featured' => false,
            ],
            [
                'category' => 'Mobile Phones',
                'name' => 'Xiaomi 14 Ultra 512GB',
                'description' => 'Xiaomi 14 Ultra with Snapdragon 8 Gen 3, 512GB storage, Leica quad camera system, 6.73" WQHD+ AMOLED. Photography-focused flagship.',
                'price' => 1399999.00,
                'previous_price' => 1499999.00,
                'brand_name' => 'Xiaomi',
                'is_new' => true,
                'is_featured' => false,
            ],

            // Tablets
            [
                'category' => 'Tablets',
                'name' => 'iPad Pro 12.9" M2 256GB',
                'description' => 'Apple iPad Pro 12.9-inch with M2 chip, 256GB storage, 12.9" Liquid Retina XDR display. Supports Apple Pencil 2 and Magic Keyboard.',
                'price' => 1599999.00,
                'previous_price' => 1699999.00,
                'brand_name' => 'Apple',
                'is_new' => false,
                'is_featured' => true,
            ],
            [
                'category' => 'Tablets',
                'name' => 'Samsung Galaxy Tab S9 Ultra 512GB',
                'description' => 'Samsung Galaxy Tab S9 Ultra with Snapdragon 8 Gen 2, 512GB storage, 14.6" Dynamic AMOLED 2X. Includes S Pen. IP68 water resistance.',
                'price' => 1499999.00,
                'previous_price' => 1599999.00,
                'brand_name' => 'Samsung',
                'is_new' => true,
                'is_featured' => false,
            ],
            [
                'category' => 'Tablets',
                'name' => 'Microsoft Surface Pro 9 256GB',
                'description' => 'Microsoft Surface Pro 9 with Intel Core i7, 16GB RAM, 256GB SSD, 13" PixelSense Flow display. 2-in-1 with kickstand and Type Cover support.',
                'price' => 1399999.00,
                'previous_price' => 1499999.00,
                'brand_name' => 'Microsoft',
                'is_new' => false,
                'is_featured' => false,
            ],
            [
                'category' => 'Tablets',
                'name' => 'Xiaomi Pad 6 Pro 256GB',
                'description' => 'Xiaomi Pad 6 Pro with Snapdragon 8+ Gen 1, 256GB storage, 11" 2.8K 144Hz display. Includes stylus support. Great value for productivity.',
                'price' => 499999.00,
                'previous_price' => 549999.00,
                'brand_name' => 'Xiaomi',
                'is_new' => false,
                'is_featured' => false,
            ],

            // Desktop Computers
            [
                'category' => 'Desktop Computers',
                'name' => 'Apple Mac Studio M2 Ultra',
                'description' => 'Apple Mac Studio with M2 Ultra chip, 64GB unified memory, 1TB SSD. Compact professional desktop for extreme workloads.',
                'price' => 4999999.00,
                'previous_price' => 5499999.00,
                'brand_name' => 'Apple',
                'is_new' => true,
                'is_featured' => true,
            ],
            [
                'category' => 'Desktop Computers',
                'name' => 'Dell Alienware Aurora R15',
                'description' => 'Dell Alienware Aurora R15 with Intel Core i9-13900KF, 32GB RAM, 2TB SSD, NVIDIA RTX 4090 24GB. Legendary gaming desktop.',
                'price' => 4499999.00,
                'previous_price' => 4899999.00,
                'brand_name' => 'Dell',
                'is_new' => false,
                'is_featured' => true,
            ],
            [
                'category' => 'Desktop Computers',
                'name' => 'HP Omen 45L',
                'description' => 'HP Omen 45L with AMD Ryzen 9 7900X, 32GB RAM, 2TB SSD, NVIDIA RTX 4080 16GB. Tool-less upgrade design with liquid cooling.',
                'price' => 3799999.00,
                'previous_price' => 3999999.00,
                'brand_name' => 'HP',
                'is_new' => true,
                'is_featured' => false,
            ],
            [
                'category' => 'Desktop Computers',
                'name' => 'Lenovo Legion Tower 7i',
                'description' => 'Lenovo Legion Tower 7i with Intel Core i7-13700KF, 32GB RAM, 1TB SSD + 2TB HDD, NVIDIA RTX 4070 Ti 12GB. RGB lighting, quiet cooling.',
                'price' => 2899999.00,
                'previous_price' => 3099999.00,
                'brand_name' => 'Lenovo',
                'is_new' => false,
                'is_featured' => false,
            ],

            // Audio Devices
            [
                'category' => 'Audio Devices',
                'name' => 'Sony WH-1000XM5',
                'description' => 'Sony WH-1000XM5 wireless noise-canceling headphones with 30-hour battery, multipoint connection, speak-to-chat. Industry-leading ANC.',
                'price' => 399999.00,
                'previous_price' => 449999.00,
                'brand_name' => 'Sony',
                'is_new' => false,
                'is_featured' => true,
            ],
            [
                'category' => 'Audio Devices',
                'name' => 'Apple AirPods Pro 2nd Gen',
                'description' => 'Apple AirPods Pro (2nd generation) with H2 chip, adaptive transparency, personalized spatial audio, USB-C charging case. Up to 6 hours listening.',
                'price' => 299999.00,
                'previous_price' => 329999.00,
                'brand_name' => 'Apple',
                'is_new' => true,
                'is_featured' => true,
            ],
            [
                'category' => 'Audio Devices',
                'name' => 'Bose QuietComfort Ultra Headphones',
                'description' => 'Bose QuietComfort Ultra with immersive spatial audio, world-class noise cancellation, 24-hour battery. Premium comfort and sound.',
                'price' => 449999.00,
                'previous_price' => 499999.00,
                'brand_name' => 'Bose',
                'is_new' => true,
                'is_featured' => false,
            ],
            [
                'category' => 'Audio Devices',
                'name' => 'Sennheiser Momentum 4 Wireless',
                'description' => 'Sennheiser Momentum 4 with 60-hour battery, adaptive noise cancellation, audiophile sound quality. Smart control app with EQ.',
                'price' => 379999.00,
                'previous_price' => 419999.00,
                'brand_name' => 'Sennheiser',
                'is_new' => false,
                'is_featured' => false,
            ],
            [
                'category' => 'Audio Devices',
                'name' => 'JBL Charge 5 Portable Speaker',
                'description' => 'JBL Charge 5 waterproof portable speaker with 20-hour playtime, powerbank feature, PartyBoost. Bold JBL Pro Sound in rugged design.',
                'price' => 179999.00,
                'previous_price' => 199999.00,
                'brand_name' => 'JBL',
                'is_new' => false,
                'is_featured' => false,
            ],

            // Computer Accessories
            [
                'category' => 'Computer Accessories',
                'name' => 'Logitech MX Master 3S',
                'description' => 'Logitech MX Master 3S wireless mouse with 8000 DPI, MagSpeed scrolling, 70-day battery, multi-device pairing. Ergonomic design for productivity.',
                'price' => 129999.00,
                'previous_price' => 149999.00,
                'brand_name' => 'Logitech',
                'is_new' => false,
                'is_featured' => true,
            ],
            [
                'category' => 'Computer Accessories',
                'name' => 'Keychron K2 Pro Mechanical Keyboard',
                'description' => 'Keychron K2 Pro 75% wireless mechanical keyboard with QMK/VIA, hot-swappable switches, RGB backlight. Mac/Windows compatible.',
                'price' => 189999.00,
                'previous_price' => 209999.00,
                'brand_name' => 'Keychron',
                'is_new' => true,
                'is_featured' => false,
            ],
            [
                'category' => 'Computer Accessories',
                'name' => 'Samsung 990 PRO 2TB NVMe SSD',
                'description' => 'Samsung 990 PRO 2TB PCIe 4.0 NVMe SSD with 7450 MB/s read, 6900 MB/s write. With heatsink for PS5 and PC gaming.',
                'price' => 199999.00,
                'previous_price' => 229999.00,
                'brand_name' => 'Samsung',
                'is_new' => false,
                'is_featured' => true,
            ],
            [
                'category' => 'Computer Accessories',
                'name' => 'LG UltraFine 27" 5K Monitor',
                'description' => 'LG UltraFine 27-inch 5K display with 5120x2880 resolution, P3 wide color, 500 nits brightness. Thunderbolt 3, 94W power delivery.',
                'price' => 1299999.00,
                'previous_price' => 1399999.00,
                'brand_name' => 'LG',
                'is_new' => false,
                'is_featured' => false,
            ],
            [
                'category' => 'Computer Accessories',
                'name' => 'Anker 777 Thunderbolt Dock',
                'description' => 'Anker 777 Thunderbolt 4 12-in-1 dock with 100W charging, dual 4K@60Hz, 10Gbps data, SD card reader. MacBook Pro perfect companion.',
                'price' => 349999.00,
                'previous_price' => 399999.00,
                'brand_name' => 'Anker',
                'is_new' => true,
                'is_featured' => false,
            ],

            // Phone Accessories
            [
                'category' => 'Phone Accessories',
                'name' => 'Apple MagSafe Charger',
                'description' => 'Apple MagSafe Charger with 15W wireless charging for iPhone 12 and later. Perfect alignment with magnetic attachment. USB-C cable included.',
                'price' => 49999.00,
                'previous_price' => 59999.00,
                'brand_name' => 'Apple',
                'is_new' => false,
                'is_featured' => true,
            ],
            [
                'category' => 'Phone Accessories',
                'name' => 'Anker 737 Power Bank 24000mAh',
                'description' => 'Anker 737 PowerCore 24000mAh with 140W total output, dual USB-C + USB-A, smart display. Charges MacBook Pro and iPhone simultaneously.',
                'price' => 189999.00,
                'previous_price' => 209999.00,
                'brand_name' => 'Anker',
                'is_new' => true,
                'is_featured' => false,
            ],
            [
                'category' => 'Phone Accessories',
                'name' => 'Spigen Ultra Hybrid Case iPhone 15 Pro',
                'description' => 'Spigen Ultra Hybrid clear case for iPhone 15 Pro with MagSafe compatibility, air cushion technology, raised bezels. Show off your iPhone color.',
                'price' => 24999.00,
                'previous_price' => 29999.00,
                'brand_name' => 'Spigen',
                'is_new' => false,
                'is_featured' => false,
            ],
            [
                'category' => 'Phone Accessories',
                'name' => 'Belkin BoostCharge Pro 3-in-1',
                'description' => 'Belkin BoostCharge Pro 3-in-1 wireless charging stand for iPhone, Apple Watch, and AirPods. 15W MagSafe fast charging. Sleek design.',
                'price' => 179999.00,
                'previous_price' => 199999.00,
                'brand_name' => 'Belkin',
                'is_new' => true,
                'is_featured' => false,
            ],

            // Gaming Devices
            [
                'category' => 'Gaming Devices',
                'name' => 'PlayStation 5 Slim Disc Edition',
                'description' => 'Sony PlayStation 5 Slim with 1TB SSD, Ultra HD Blu-ray, DualSense controller. 4K gaming up to 120fps, ray tracing, 3D audio.',
                'price' => 649999.00,
                'previous_price' => 699999.00,
                'brand_name' => 'Sony',
                'is_new' => true,
                'is_featured' => true,
            ],
            [
                'category' => 'Gaming Devices',
                'name' => 'Xbox Series X 1TB',
                'description' => 'Microsoft Xbox Series X with 1TB custom NVMe SSD, 4K gaming at 60fps up to 120fps, Quick Resume, backward compatibility. Carbon Black.',
                'price' => 599999.00,
                'previous_price' => 649999.00,
                'brand_name' => 'Microsoft',
                'is_new' => false,
                'is_featured' => true,
            ],
            [
                'category' => 'Gaming Devices',
                'name' => 'Nintendo Switch OLED Model',
                'description' => 'Nintendo Switch OLED with 7-inch OLED screen, 64GB storage, enhanced audio, wired LAN dock. White Joy-Con. Handheld, tabletop, TV modes.',
                'price' => 399999.00,
                'previous_price' => 429999.00,
                'brand_name' => 'Nintendo',
                'is_new' => false,
                'is_featured' => false,
            ],
            [
                'category' => 'Gaming Devices',
                'name' => 'Steam Deck OLED 1TB',
                'description' => 'Valve Steam Deck OLED 1TB with 7.4" HDR OLED, 90Hz refresh, AMD APU, 50Wh battery. Play your Steam library anywhere. Limited Edition.',
                'price' => 799999.00,
                'previous_price' => 849999.00,
                'brand_name' => 'Valve',
                'is_new' => true,
                'is_featured' => true,
            ],
            [
                'category' => 'Gaming Devices',
                'name' => 'Razer Wolverine V2 Pro Controller',
                'description' => 'Razer Wolverine V2 Pro wireless controller for PS5/PC with hypertrigger, 6 remappable buttons, 28-hour battery. Tournament-grade precision.',
                'price' => 299999.00,
                'previous_price' => 329999.00,
                'brand_name' => 'Razer',
                'is_new' => false,
                'is_featured' => false,
            ],

            // Office Equipment
            [
                'category' => 'Office Equipment',
                'name' => 'HP LaserJet Pro M404dn',
                'description' => 'HP LaserJet Pro M404dn monochrome laser printer with 40ppm, duplex printing, Ethernet, USB. 250-sheet tray, HP Wolf Pro Security.',
                'price' => 249999.00,
                'previous_price' => 279999.00,
                'brand_name' => 'HP',
                'is_new' => false,
                'is_featured' => false,
            ],
            [
                'category' => 'Office Equipment',
                'name' => 'Canon imageCLASS MF445dw',
                'description' => 'Canon imageCLASS MF445dw all-in-one laser printer with 40ppm, duplex scan/print/copy/fax, 5-inch touchscreen, mobile printing.',
                'price' => 349999.00,
                'previous_price' => 379999.00,
                'brand_name' => 'Canon',
                'is_new' => true,
                'is_featured' => false,
            ],
            [
                'category' => 'Office Equipment',
                'name' => 'Brother MFC-L3770CDW',
                'description' => 'Brother MFC-L3770CDW color laser all-in-one with 25ppm, duplex print/scan/copy/fax, 3.7" touchscreen, 250-sheet capacity. Compact office solution.',
                'price' => 399999.00,
                'previous_price' => 429999.00,
                'brand_name' => 'Brother',
                'is_new' => false,
                'is_featured' => false,
            ],
        ];

        foreach ($products as $productData) {
            $categoryName = $productData['category'];
            unset($productData['category']);

            if (isset($categories[$categoryName])) {
                $product = Product::create([
                    'category_id' => $categories[$categoryName]->id,
                    ...$productData,
                    'slug' => Str::slug($productData['name']),
                    'views' => rand(100, 10000),
                ]);

                // Create product answers for category questions
                $questions = $categories[$categoryName]->questions;
                foreach ($questions as $question) {
$answerValue = match ($question->question) {
                        'Graphics Card' => str_contains($product->name, 'RTX') ? 'NVIDIA ' . str_replace('RTX', '', $product->name . ' has RTX graphics') : (str_contains($product->name, '40') ? 'NVIDIA RTX 40' . rand(50, 90) . 'GB' : (str_contains($product->name, '30') ? 'NVIDIA RTX 30' . rand(50, 90) . 'GB' : 'Integrated graphics')),
                        'RAM Size' => str_contains($product->name, 'MacBook Pro 16') ? '36GB' : (str_contains($product->name, 'Dell XPS') ? '32GB' : (str_contains($product->name, 'ASUS ROG') ? '16GB' : (str_contains($product->name, 'ThinkPad') ? '32GB' : (str_contains($product->name, 'HP Spectre') ? '16GB' : rand(8, 32) . 'GB')))),
                        'Screen Size' => str_contains($product->name, '16"') ? '16 inches' : (str_contains($product->name, '15.6"') ? '15.6 inches' : (str_contains($product->name, '14"') ? '14 inches' : (str_contains($product->name, '12.9"') ? '12.9 inches' : rand(12, 18) . ' inches'))),
                        'Storage Capacity' => str_contains($product->name, '256GB') ? '256GB' : (str_contains($product->name, '512GB') ? '512GB' : rand(128, 1024) . 'GB'),
                        'Camera Megapixels' => str_contains($product->name, '200MP') ? '200MP' : (str_contains($product->name, '48MP') ? '48MP' : (str_contains($product->name, '50MP') ? '50MP' : rand(12, 108) . 'MP')),
                        'Battery Capacity' => str_contains($product->name, '10000mAh') ? '10000mAh' : (str_contains($product->name, '24000mAh') ? '24000mAh' : rand(3000, 5000) . 'mAh'),
                        '5G Support' => 'Yes',
                        'Cellular Connectivity' => 'Yes',
                        'Stylus Support' => 'Yes',
                        'Power Supply' => '900W',
                        'Connection Type' => match ($question->question) {
                            'Computer Accessories' => 'USB-C',
                            'Phone Accessories' => 'USB-C',
                            default => 'USB-A',
                        },
                        'Compatibility' => match ($categoryName) {
                            'Laptops' => 'Compatible with most laptops',
                            'Mobile Phones' => 'Compatible with latest smartphones',
                            default => 'Universal compatibility',
                        },
                        'Noise Cancellation' => 'Yes',
                        'Battery Life' => str_contains($product->name, 'WH-1000XM5') ? '30 hours' : (str_contains($product->name, 'AirPods Pro') ? '6 hours' : (str_contains($product->name, 'QuietComfort') ? '24 hours' : (str_contains($product->name, 'Momentum 4') ? '60 hours' : (str_contains($product->name, 'Charge 5') ? '20 hours' : rand(5, 30) . ' hours')))),
                        'Wireless Charging' => 'Yes',
                        'MagSafe Support' => 'Yes',
                        'Print Speed' => '40 ppm',
                        'Paper Capacity' => '250 sheets',
                        default => 'Sample answer',
                    };

                    ProductAnswer::create([
                        'product_id' => $product->id,
                        'category_question_id' => $question->id,
                        'answer' => $answerValue,
                    ]);
                }

                // Create placeholder product images (using placeholder URLs since we don't have actual images)
                // In production, you would upload actual images via the Filament admin
                for ($i = 1; $i <= rand(1, 3); $i++) {
                    $product->images()->create([
                        'image_url' => "https://picsum.photos/seed/{$product->id}-{$i}/800/600.jpg",
                    ]);
                }
            }
        }

        $this->command->info('Demo products created successfully!');
    }
}
