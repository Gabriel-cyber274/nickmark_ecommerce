<?php

namespace App\Filament\Resources\DispatchFees\Pages;

use App\Filament\Resources\DispatchFees\DispatchFeeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDispatchFee extends ViewRecord
{
    protected static string $resource = DispatchFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
