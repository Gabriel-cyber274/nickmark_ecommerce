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
        // Sample reviews
        $reviews = [
            [
                'name' => 'Alice Johnson',
                'review' => 'Great company to work with. Highly professional team!',
                'relationship' => 'Client',
            ],
            [
                'name' => 'Bob Smith',
                'review' => 'Had a wonderful experience collaborating with them.',
                'relationship' => 'Partner',
            ],
            [
                'name' => 'Charlie Brown',
                'review' => 'The team is very responsive and skilled.',
                'relationship' => 'Employee',
            ],
            [
                'name' => 'Diana Prince',
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
