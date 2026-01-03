<?php

namespace App\Filament\Resources\DispatchFees\Pages;

use App\Filament\Resources\DispatchFees\DispatchFeeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDispatchFee extends EditRecord
{
    protected static string $resource = DispatchFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
