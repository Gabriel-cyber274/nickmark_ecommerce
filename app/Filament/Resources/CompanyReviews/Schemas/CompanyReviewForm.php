<?php

namespace App\Filament\Resources\CompanyReviews\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CompanyReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('review')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('relationship')
                    ->required(),
            ]);
    }
}
