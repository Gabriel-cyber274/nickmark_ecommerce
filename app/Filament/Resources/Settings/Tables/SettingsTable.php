<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dispatch_return')
                    ->label('Dispatch Return')
                    ->limit(50),
                TextColumn::make('is_paystack_enabled')
                    ->label('Paystack Enabled')
                    ->badge()
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                TextColumn::make('is_whatsapp_enabled')
                    ->label('WhatsApp Enabled')
                    ->badge()
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                TextColumn::make('account_name')
                    ->label('Account Name'),
                TextColumn::make('account_number')
                    ->label('Account Number'),
                TextColumn::make('bank_name')
                    ->label('Bank Name'),
                TextColumn::make('phone_number1')
                    ->label('Phone 1')
                    ->toggleable(),
                TextColumn::make('phone_number2')
                    ->label('Phone 2')
                    ->toggleable(),
                TextColumn::make('phone_number3')
                    ->label('Phone 3')
                    ->toggleable(),
                TextColumn::make('whatsapp_number')
                    ->label('WhatsApp Number')
                    ->toggleable(),
                TextColumn::make('facebook_url')
                    ->label('Facebook URL')
                    ->toggleable(),
                TextColumn::make('twitter_url')
                    ->label('Twitter URL')
                    ->toggleable(),
                TextColumn::make('instagram_url')
                    ->label('Instagram URL')
                    ->toggleable(),
                TextColumn::make('youtube_url')
                    ->label('YouTube URL')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
