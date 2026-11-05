<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyReview;

class CompanyReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample reviews with Nigerian names
        $reviews = [
            [
                'name' => 'Chidi Nnamdi',
                'review' => 'Great company to work with. Highly professional team!',
                'relationship' => 'Client',
            ],
            [
                'name' => 'Aisha Bello',
                'review' => 'Had a wonderful experience collaborating with them.',
                'relationship' => 'Client',
            ],
            [
                'name' => 'Kelechi Okafor',
                'review' => 'The team is very responsive and skilled.',
                'relationship' => 'Client',
            ],
            [
                'name' => 'Zainab Ibrahim',
                'review' => 'Excellent services and support throughout.',
                'relationship' => 'Client',
            ],
        ];

        // Insert into the database
        foreach ($reviews as $review) {
            CompanyReview::create($review);
        }
    }
}
