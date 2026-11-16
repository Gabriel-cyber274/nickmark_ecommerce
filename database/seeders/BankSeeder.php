<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        Bank::create([
            'account_number' => '5054060087',
            'bank_name' => 'Moniepoint',
            'account_name' => 'Nick mark Technologies',
            'is_active' => true,
        ]);
    }
}
