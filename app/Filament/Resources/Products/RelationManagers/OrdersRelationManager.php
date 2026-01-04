<?php

namespace App\Filament\Resources\Products\RelationManagers;

use App\Filament\Resources\Orders\OrderResource as OrdersOrderResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class OrderItemsRelationManager extends RelationManager
{
    // Use the orderItems() relationship from Product model
    protected static string $relationship = 'orderItems';

    protected static ?string $title = 'Orders Containing This Product';

    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.reference')
                    ->label('Order Ref')
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Quantity'),

                TextColumn::make('total')
                    ->label('Product Total')
                    ->getStateUsing(fn($record) => $record->quantity * ($record->product?->price ?? 0))
                    ->money('NGN'),


                BadgeColumn::make('order.status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => ['paid', 'completed'],
                        'danger' => 'cancelled',
                    ])
                    ->label('Status'),

                TextColumn::make('order.created_at')
                    ->dateTime()
                    ->label('Order Date'),
            ])
            ->recordActions([
                ViewAction::make('viewOrder')
                    ->label('View Order')
                    ->url(fn($record) => OrdersOrderResource::getUrl('view', ['record' => $record->order_id]))
                    ->openUrlInNewTab(),
            ]);
    }
}
