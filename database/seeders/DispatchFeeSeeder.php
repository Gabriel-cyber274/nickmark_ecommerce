<?php

namespace Database\Seeders;

use App\Models\DispatchFee;
use App\Models\State;
use Illuminate\Database\Seeder;

class DispatchFeeSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * DEFAULT DISPATCH FEE (fallback)
         */
        $defaultAmount = 5000;

        /**
         * STATE-SPECIFIC PRICING
         */
        $statePricing = [
            'Lagos' => 2000,
            'Ogun' => 3000,
            'Oyo' => 3500,
            'Rivers' => 4000,
            'Delta' => 4200,
            'Edo' => 4200,
            'Federal Capital Territory (FCT)' => 3000,
        ];

        State::chunk(50, function ($states) use ($defaultAmount, $statePricing) {
            foreach ($states as $state) {
                DispatchFee::updateOrCreate(
                    [
                        'state_id' => $state->id,
                        'city_id' => null,
                    ],
                    [
                        'amount' => $statePricing[$state->name] ?? $defaultAmount,
                    ]
                );
            }
        });
    }
}
