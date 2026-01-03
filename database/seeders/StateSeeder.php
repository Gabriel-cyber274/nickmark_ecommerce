<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $data = $this->nigeriaData();

        foreach ($data['Nigeria']['states'] as $state) {
            State::updateOrCreate(
                ['name' => $state['name']],
                ['capital' => $state['capital']]
            );
        }
    }

    private function nigeriaData(): array
    {
        return json_decode(file_get_contents(database_path('seeders/data/nigeria.json')), true);
    }
}
