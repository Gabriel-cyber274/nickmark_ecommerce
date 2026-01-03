<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $data = $this->nigeriaData();

        foreach ($data['Nigeria']['states'] as $stateData) {
            $state = State::where('name', $stateData['name'])->first();

            if (! $state) {
                continue;
            }

            foreach ($stateData['lgas'] as $lga) {
                City::updateOrCreate(
                    [
                        'state_id' => $state->id,
                        'name' => $lga,
                    ]
                );
            }
        }
    }

    private function nigeriaData(): array
    {
        return json_decode(file_get_contents(database_path('seeders/data/nigeria.json')), true);
    }
}
