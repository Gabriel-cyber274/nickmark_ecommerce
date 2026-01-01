<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /* =======================
                 *  BASIC PRODUCT INFO
                 * ======================= */
                TextEntry::make('name')
                    ->label('Product Name'),

                TextEntry::make('category.name')
                    ->label('Category'),

                TextEntry::make('price')
                    ->label('Price')
                    ->money('NGN'),

                TextEntry::make('previous_price')
                    ->label('Previous Price')
                    ->money('NGN')
                    ->placeholder('—'),


                IconEntry::make('is_new')
                    ->label('Is New')
                    ->boolean(),


                IconEntry::make('is_featured')
                    ->label('Is Featured')
                    ->boolean(),

                TextEntry::make('brand_name'),


                TextEntry::make('description')
                    ->columnSpanFull(),

                /* =======================
                 *  PRODUCT IMAGES
                 * ======================= */
                ImageEntry::make('images')
                    ->label('Product Images')
                    ->getStateUsing(
                        fn($record) =>
                        $record->images->pluck('image_url')->toArray()
                    )
                    ->disk('public')
                    ->square()
                    ->height(120)
                    ->columnSpanFull(),

                /* =======================
                 *  CATEGORY QUESTIONS & ANSWERS
                 * ======================= */
                RepeatableEntry::make('answers')
                    ->label('Category Questions')
                    ->schema([
                        TextEntry::make('question.question')
                            ->label('Question')
                            ->weight('bold')
                            ->color('primary'),

                        TextEntry::make('answer')
                            ->label('Answer')
                            ->placeholder('No answer provided')
                            ->formatStateUsing(function ($state, $record) {
                                // Check if question exists
                                if (!$record->question) {
                                    return $state ?: '—';
                                }

                                // Format boolean answers for better readability
                                if ($record->question->type === 'boolean') {
                                    return $state === '1' || $state === 1 || $state === true ? 'Yes' : 'No';
                                }

                                return $state ?: '—';
                            }),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->visible(fn($record) => $record->answers->isNotEmpty()),

                /* =======================
                 *  TIMESTAMPS
                 * ======================= */
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime(),
            ]);
    }
}
