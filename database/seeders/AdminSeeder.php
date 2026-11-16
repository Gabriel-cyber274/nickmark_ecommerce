<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@nickmark.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => \App\Models\Admin::ROLE_SUPER_ADMIN,
            ]
        );

        Admin::updateOrCreate(
            ['email' => 'product_admin@nickmark.com'],
            [
                'name' => 'Product Admin',
                'password' => Hash::make('password'),
                'role' => \App\Models\Admin::ROLE_PRODUCT_ADMIN,
            ]
        );
    }
}
