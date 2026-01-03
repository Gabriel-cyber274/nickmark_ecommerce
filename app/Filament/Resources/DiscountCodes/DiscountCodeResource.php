<?php

namespace App\Filament\Resources\DiscountCodes;

use App\Filament\Resources\DiscountCodes\Pages\CreateDiscountCode;
use App\Filament\Resources\DiscountCodes\Pages\EditDiscountCode;
use App\Filament\Resources\DiscountCodes\Pages\ListDiscountCodes;
use App\Filament\Resources\DiscountCodes\Pages\ViewDiscountCode;
use App\Filament\Resources\DiscountCodes\Schemas\DiscountCodeForm;
use App\Filament\Resources\DiscountCodes\Schemas\DiscountCodeInfolist;
use App\Filament\Resources\DiscountCodes\Tables\DiscountCodesTable;
use App\Models\DiscountCode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DiscountCodeResource extends Resource
{
    protected static ?string $model = DiscountCode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'code';
    protected static ?string $navigationLabel = 'Discount Codes';
    protected static ?string $pluralLabel = 'Discount Codes';
    protected static ?string $modelLabel = 'Discount Code';

    public static function form(Schema $schema): Schema
    {
        return DiscountCodeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DiscountCodeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiscountCodesTable::configure($table);
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
            'index' => ListDiscountCodes::route('/'),
            'create' => CreateDiscountCode::route('/create'),
            'view' => ViewDiscountCode::route('/{record}'),
            'edit' => EditDiscountCode::route('/{record}/edit'),
        ];
    }
}
