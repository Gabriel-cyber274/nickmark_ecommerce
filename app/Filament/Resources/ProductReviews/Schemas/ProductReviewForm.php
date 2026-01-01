<?php

namespace App\Filament\Resources\ProductReviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->relationship('product', 'name')
                    ->preload()
                    ->searchable()
                    ->required(),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->preload()
                    ->searchable()
                    ->nullable(),

                TextInput::make('name')
                    ->label('Reviewer Name')
                    ->maxLength(255)
                    ->nullable(),

                Select::make('rate')
                    ->label('Rating')
                    ->options([
                        1 => '⭐ 1',
                        2 => '⭐⭐ 2',
                        3 => '⭐⭐⭐ 3',
                        4 => '⭐⭐⭐⭐ 4',
                        5 => '⭐⭐⭐⭐⭐ 5',
                    ])
                    ->required(),

                Textarea::make('comment')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
