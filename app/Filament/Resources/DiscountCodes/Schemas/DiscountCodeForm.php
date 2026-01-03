<?php

namespace App\Filament\Resources\DiscountCodes\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class DiscountCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Discount Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->default(fn() => strtoupper(Str::random(8))) // auto-generate 8 character code
                    ->readOnly(), // prevents user from editing

                TextInput::make('min_amount')
                    ->label('Minimum Cart Amount')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->prefix('₦'),

                TextInput::make('discount_amount')
                    ->label('Discount Amount')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->prefix('₦'),

                DateTimePicker::make('expires_at')
                    ->label('Expiry Date')
                    ->required()
                // ->minDate(now())
                ,
            ]);
    }
}
