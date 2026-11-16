<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->dehydrated(),
                Textarea::make('description'),
                FileUpload::make('image')
                    ->label('Category Image')
                    ->disk('public')
                    ->image()
                    ->directory('categories')
                    ->visibility('public')
                    ->required(fn(string $context): bool => $context === 'create')
                    ->dehydrated(false)
                    ->afterStateHydrated(function (FileUpload $component, ?Category $record) {
                        if (! $record || blank($record->image)) {
                            return;
                        }

                        $component->state($record->image);
                    })
                    ->getUploadedFileUsing(function (FileUpload $component, $file): ?array {
                        if ($file instanceof TemporaryUploadedFile) {
                            return [
                                'name' => $file->getClientOriginalName(),
                                'size' => $file->getSize(),
                                'type' => $file->getMimeType(),
                                'url'  => $file->temporaryUrl(),
                            ];
                        }

                        if (is_string($file)) {
                            $isUrl = Str::startsWith($file, ['http://', 'https://']);

                            return [
                                'name' => basename(parse_url($file, PHP_URL_PATH) ?: $file),
                                'size' => (! $isUrl && Storage::disk('public')->exists($file))
                                    ? Storage::disk('public')->size($file)
                                    : 0,
                                'type' => 'image/jpeg',
                                'url'  => $isUrl ? $file : Storage::disk('public')->url($file),
                            ];
                        }

                        return null;
                    })
                    ->deleteUploadedFileUsing(fn () => null),
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
