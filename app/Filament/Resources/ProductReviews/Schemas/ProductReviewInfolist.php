<?php

namespace App\Filament\Resources\ProductReviews\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductReviewInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Review Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Product'),

                                TextEntry::make('user.name')
                                    ->label('User')
                                    ->placeholder('Guest'),

                                TextEntry::make('name')
                                    ->label('Guest Name')
                                    ->placeholder('—'),

                                TextEntry::make('rate')
                                    ->label('Rating')
                                    ->formatStateUsing(
                                        fn($state) => str_repeat('⭐', (int) $state)
                                    ),
                            ]),

                        TextEntry::make('comment')
                            ->label('Comment')
                            ->columnSpanFull(),
                    ]),

                Section::make('Meta')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime(),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime(),
                            ]),
                    ])
                    ->collapsed(),
            ]);
    }
}
