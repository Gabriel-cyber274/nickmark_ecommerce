<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Filament\Resources\Products\ProductResource;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Illuminate\Database\Eloquent\Model;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Order Items';

    protected static ?string $relatedResource = null; // inline handling

    // public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    // {
    //     return auth()->check();
    // }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable(),

                TextColumn::make('quantity')
                    ->sortable(),

                TextColumn::make('product.price')
                    ->label('Unit Price')
                    ->money('NGN'),

                TextColumn::make('total')
                    ->label('Total')
                    ->getStateUsing(
                        fn($record) => $record->quantity * ($record->product?->price ?? 0)
                    )
                    ->money('NGN'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Add Order Item')
                    ->form([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->required(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $this->getOwnerRecord()->items()->create($data);
                    }),
            ])
            ->recordActions([
                ViewAction::make('viewProduct')
                    ->label('View Product')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => ProductResource::getUrl(
                        'view',
                        ['record' => $record->product_id]
                    ))
                    ->openUrlInNewTab(),

                EditAction::make()
                    ->form([
                        Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->required(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->minValue(1)
                            ->required(),
                    ]),

                DeleteAction::make(),
            ]);
    }
}
