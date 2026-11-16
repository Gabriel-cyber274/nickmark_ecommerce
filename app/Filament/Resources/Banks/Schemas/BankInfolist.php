<?php

namespace App\Filament\Resources\Banks\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BankInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('bank_name'),
                TextEntry::make('account_number'),
                TextEntry::make('account_name'),
                IconEntry::make('is_active')->boolean(),
            ]);
    }
}
