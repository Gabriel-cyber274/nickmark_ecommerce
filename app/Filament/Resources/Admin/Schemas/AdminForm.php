<?php

namespace App\Filament\Resources\Admin\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                Toggle::make('is_admin')
                    ->label('Is Admin')
                    ->required(),
                TextInput::make('password')
                    ->password()
                // ->required()
                ,
            ]);
    }
}
