<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use App\Models\CategoryQuestion;
use App\Models\Product;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('price')
                            ->numeric()
                            ->prefix('₦')
                            ->required(),

                        TextInput::make('previous_price')
                            ->label('Previous Price')
                            ->numeric()
                            ->prefix('₦'),

                        TextInput::make('brand_name')
                            ->required(),

                        Toggle::make('is_new')
                            ->label('Is New')
                            ->default(false), // optional, defaults to false

                        // Toggle::make('is_featured')
                        //     ->label('Is Featured')
                        //     ->default(false),

                        Toggle::make('is_featured')
                            ->label('Is Featured')
                            ->default(false)
                            ->disabled(function ($context, $record) {
                                // If editing an existing product and it's already featured, allow toggling off
                                if ($context === 'edit' && $record && $record->is_featured) {
                                    return false;
                                }

                                // Check if featured limit is reached
                                $featuredCount = Product::where('is_featured', true)->count();
                                return $featuredCount >= 10;
                            })
                            ->helperText(function () {
                                $featuredCount = Product::where('is_featured', true)->count();
                                $remaining = 10 - $featuredCount;

                                if ($featuredCount >= 10) {
                                    return "Featured products limit reached (10/10). Unfeature some products first.";
                                }

                                return "{$featuredCount}/10 featured products used. {$remaining} spots remaining.";
                            }),

                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                // Clear previous answers when category changes
                                $set('answers', []);

                                // Pre-populate answers array with questions from the selected category
                                if ($state) {
                                    $questions = CategoryQuestion::where('category_id', $state)->get();
                                    $answers = [];

                                    foreach ($questions as $question) {
                                        $answers[] = [
                                            'category_question_id' => $question->id,
                                            'answer' => '',
                                        ];
                                    }

                                    $set('answers', $answers);
                                }
                            }),
                    ])
                    ->columns(2),

                Section::make('Product Images')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Product Images')
                            ->disk('public')
                            ->multiple()
                            ->image()
                            ->maxFiles(3)
                            ->reorderable()
                            ->directory('products')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Section::make('Category Questions')
                    ->schema([
                        Repeater::make('answers')
                            ->label('Answer Category Questions')
                            ->relationship('answers')
                            ->schema([
                                Hidden::make('category_question_id'),

                                Placeholder::make('question_display')
                                    ->label('Question')
                                    ->content(function (Get $get) {
                                        $questionId = $get('category_question_id');
                                        if ($questionId) {
                                            $question = CategoryQuestion::find($questionId);
                                            return $question ? $question->question : '';
                                        }
                                        return '';
                                    }),

                                TextInput::make('answer')
                                    ->label('Answer')
                                    ->required(function (Get $get) {
                                        $questionId = $get('category_question_id');
                                        if ($questionId) {
                                            $question = CategoryQuestion::find($questionId);
                                            return $question ? $question->is_required : false;
                                        }
                                        return false;
                                    })
                                    ->visible(function (Get $get) {
                                        $questionId = $get('category_question_id');
                                        if ($questionId) {
                                            $question = CategoryQuestion::find($questionId);
                                            return $question && $question->type === 'text';
                                        }
                                        return false;
                                    })
                                    ->columnSpanFull(),

                                TextInput::make('answer_number')
                                    ->label('Answer')
                                    ->numeric()
                                    ->required(function (Get $get) {
                                        $questionId = $get('category_question_id');
                                        if ($questionId) {
                                            $question = CategoryQuestion::find($questionId);
                                            return $question ? $question->is_required : false;
                                        }
                                        return false;
                                    })
                                    ->visible(function (Get $get) {
                                        $questionId = $get('category_question_id');
                                        if ($questionId) {
                                            $question = CategoryQuestion::find($questionId);
                                            return $question && $question->type === 'number';
                                        }
                                        return false;
                                    })
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('answer', $state);
                                    })
                                    ->live()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),

                                Toggle::make('answer_boolean')
                                    ->label('Answer (Yes/No)')
                                    ->required(function (Get $get) {
                                        $questionId = $get('category_question_id');
                                        if ($questionId) {
                                            $question = CategoryQuestion::find($questionId);
                                            return $question ? $question->is_required : false;
                                        }
                                        return false;
                                    })
                                    ->visible(function (Get $get) {
                                        $questionId = $get('category_question_id');
                                        if ($questionId) {
                                            $question = CategoryQuestion::find($questionId);
                                            return $question && $question->type === 'boolean';
                                        }
                                        return false;
                                    })
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('answer', $state ? '1' : '0');
                                    })
                                    ->live()
                                    ->dehydrated(false)
                                    ->columnSpanFull(),
                            ])
                            ->defaultItems(0)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['category_question_id']) && $state['category_question_id']
                                    ? CategoryQuestion::find($state['category_question_id'])?->question
                                    : 'Answer'
                            )
                            ->columnSpanFull()
                            ->hidden(fn(Get $get) => !$get('category_id')),
                    ]),
            ]);
    }
}
