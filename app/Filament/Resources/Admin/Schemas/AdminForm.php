<?php

namespace App\Filament\Resources\Admin\Schemas;

use App\Models\Admin;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                Select::make('role')
                    ->label('Role')
                    ->options([
                        Admin::ROLE_SUPER_ADMIN => 'Super Admin',
                        Admin::ROLE_PRODUCT_ADMIN => 'Product Admin',
                    ])
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                // ->required()
                ,
            ]);
    }
}
