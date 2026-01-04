<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Orders\OrderResource as OrdersOrderResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class OrdersRelationManager extends RelationManager
{
    // Use the orders() relationship on User model
    protected static string $relationship = 'orders';

    protected static ?string $title = 'User Orders';

    protected static ?string $recordTitleAttribute = 'reference';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')
                    ->label('Order Ref')
                    ->searchable(),

                TextColumn::make('total')
                    ->money('NGN')
                    ->label('Order Total'),

                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => ['paid', 'completed'],
                        'danger' => 'cancelled',
                    ])
                    ->label('Status'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Order Date'),
            ])
            ->recordActions([
                ViewAction::make('viewOrder')
                    ->label('View Order')
                    ->url(fn($record) => OrdersOrderResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
            ]);
    }
}
