<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                Textarea::make('description'),
                // TextInput::make('image')->url(),

                FileUpload::make('image')
                    ->label('Category Image')
                    ->disk('public')
                    ->image()
                    ->directory('categories')
                    ->visibility('public')
                    ->required(fn(string $context): bool => $context === 'create'),

                Repeater::make('questions')
                    ->relationship('questions')
                    ->label('Questions')
                    ->schema([
                        TextInput::make('question')->required(),
                        Select::make('type')
                            ->options([
                                'text' => 'Text',
                                // 'number' => 'Number',
                                // 'boolean' => 'Yes/No',
                            ])
                            ->required(),
                        Toggle::make('is_required')->label('Required?'),
                    ])
                    ->columns(3)
                    ->createItemButtonLabel('Add Question'),
            ]);
    }
}
