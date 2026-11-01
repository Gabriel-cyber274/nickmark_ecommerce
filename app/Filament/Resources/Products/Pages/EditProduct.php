<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\Pages\Concerns\SyncsProductImages;
use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    use SyncsProductImages;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->syncProductImages();
    }
}