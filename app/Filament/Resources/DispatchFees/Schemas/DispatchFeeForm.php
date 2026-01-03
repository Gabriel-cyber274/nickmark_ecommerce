<?php

namespace App\Filament\Resources\DispatchFees\Schemas;

use App\Models\City;
use App\Models\State;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DispatchFeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('state_id')
                    ->label('State')
                    ->options(State::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),

                Select::make('city_id')
                    ->label('City (Optional)')
                    ->options(function ($get) {
                        $stateId = $get('state_id');
                        return $stateId ? City::where('state_id', $stateId)->pluck('name', 'id') : [];
                    })
                    ->nullable()
                    ->searchable(),

                TextInput::make('amount')
                    ->label('Dispatch Fee Amount')
                    ->numeric()
                    ->required()
                    ->minValue(0),
            ]);
    }
}
