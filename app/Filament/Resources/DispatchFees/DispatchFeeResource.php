<?php

namespace App\Filament\Resources\DispatchFees;

use App\Filament\Resources\DispatchFees\Pages\CreateDispatchFee;
use App\Filament\Resources\DispatchFees\Pages\EditDispatchFee;
use App\Filament\Resources\DispatchFees\Pages\ListDispatchFees;
use App\Filament\Resources\DispatchFees\Pages\ViewDispatchFee;
use App\Filament\Resources\DispatchFees\Schemas\DispatchFeeForm;
use App\Filament\Resources\DispatchFees\Schemas\DispatchFeeInfolist;
use App\Filament\Resources\DispatchFees\Tables\DispatchFeesTable;
use App\Models\DispatchFee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DispatchFeeResource extends Resource
{
    protected static ?string $model = DispatchFee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $recordTitleAttribute = 'state_id';
    protected static ?string $navigationLabel = 'Dispatch Fees';
    protected static ?string $pluralLabel = 'Dispatch Fees';
    protected static ?string $modelLabel = 'Dispatch Fee';

    public static function form(Schema $schema): Schema
    {
        return DispatchFeeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DispatchFeeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DispatchFeesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDispatchFees::route('/'),
            'create' => CreateDispatchFee::route('/create'),
            'view' => ViewDispatchFee::route('/{record}'),
            'edit' => EditDispatchFee::route('/{record}/edit'),
        ];
    }
}
