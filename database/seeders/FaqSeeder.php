<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'How do I place an order?',
                'answer' => 'Browse our products, add your items to the cart, and proceed to checkout. Enter your delivery details and complete your payment to finalize your order.',
            ],
            [
                'question' => 'Do I need an account to shop?',
                'answer' => 'You can checkout as a guest, but creating an account lets you track orders, save your delivery address, and enjoy faster checkout next time.',
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept bank transfer, card payments, and other secure payment options shown at checkout.',
            ],
            [
                'question' => 'How long does delivery take?',
                'answer' => 'Delivery usually takes 2-5 business days within Nigeria, depending on your location. Estimated delivery dates are shown at checkout.',
            ],
            [
                'question' => 'What is your return policy?',
                'answer' => 'You may return unused items in original packaging within 7 days of delivery for a refund or exchange. Contact our support team to start a return.',
            ],
            [
                'question' => 'How can I contact customer support?',
                'answer' => 'Reach us through the contact form on our website or via the support email listed on the site. We typically respond within 24 hours.',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
