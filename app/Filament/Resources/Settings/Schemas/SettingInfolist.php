<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('dispatch_return')->columnSpanFull(),
                TextEntry::make('account_number'),
                TextEntry::make('bank_name'),
                TextEntry::make('account_name'),
                TextEntry::make('phone_number1')
                    ->label('Phone Number 1'),
                TextEntry::make('phone_number2')
                    ->label('Phone Number 2'),
                TextEntry::make('phone_number3')
                    ->label('Phone Number 3'),
                TextEntry::make('whatsapp_number')
                    ->label('WhatsApp Number'),
                IconEntry::make('is_paystack_enabled')->boolean(),
                IconEntry::make('is_whatsapp_enabled')->boolean(),
            ]);
    }
}