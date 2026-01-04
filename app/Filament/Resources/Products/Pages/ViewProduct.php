<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Widgets\ProductSalesProgress;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected $queryString = ['activeRelationManager'];

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }


    protected function getFooterWidgets(): array
    {

        return [
            ProductSalesProgress::class
        ];
    }
}
