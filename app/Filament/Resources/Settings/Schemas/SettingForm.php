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
                Toggle::make('is_paystack_enabled'),
                Toggle::make('is_whatsapp_enabled'),
            ]);
    }
}