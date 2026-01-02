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
                'answer' => 'Browse our products, add the items you want to your cart, and proceed to checkout to complete your purchase.'
            ],
            [
                'question' => 'Do I need an account to make a purchase?',
                'answer' => 'You can place an order as a guest, but creating an account allows you to track orders and manage your details easily.'
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept secure online payments including debit/credit cards and other supported payment options shown at checkout.'
            ],
            [
                'question' => 'Is my payment information secure?',
                'answer' => 'Yes, all payments are processed through secure and encrypted payment gateways to protect your information.'
            ],
            [
                'question' => 'How long does delivery take?',
                'answer' => 'Delivery times depend on your location. Estimated delivery dates are shown during checkout and in your order confirmation.'
            ],
            [
                'question' => 'Do you offer nationwide delivery?',
                'answer' => 'Yes, we deliver to multiple locations. Available delivery options will be displayed during checkout.'
            ],
            [
                'question' => 'How can I track my order?',
                'answer' => 'After your order is shipped, you will receive tracking details via email or can check your order status on the website.'
            ],
            [
                'question' => 'What is your return and refund policy?',
                'answer' => 'We accept returns on eligible items within the specified return period. Please ensure items are unused and in original packaging.'
            ],
            [
                'question' => 'What should I do if I receive a damaged item?',
                'answer' => 'If your item arrives damaged, please contact our support team immediately with your order details for assistance.'
            ],
            [
                'question' => 'How can I contact customer support?',
                'answer' => 'You can reach us through the contact page or support email listed on the website. We’re happy to help.'
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
