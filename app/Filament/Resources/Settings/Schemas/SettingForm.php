<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('dispatch_return')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('account_number'),
                TextInput::make('bank_name'),
                TextInput::make('account_name'),
                TextInput::make('phone_number1')
                    ->label('Phone Number 1')
                    ->tel(),
                TextInput::make('phone_number2')
                    ->label('Phone Number 2')
                    ->tel(),
                TextInput::make('phone_number3')
                    ->label('Phone Number 3')
                    ->tel(),
                Toggle::make('is_paystack_enabled'),
                Toggle::make('is_whatsapp_enabled'),
            ]);
    }
}