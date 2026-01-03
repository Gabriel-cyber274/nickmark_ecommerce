<?php

namespace App\Filament\Resources\DispatchFees\Pages;

use App\Filament\Resources\DispatchFees\DispatchFeeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDispatchFees extends ListRecords
{
    protected static string $resource = DispatchFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
