<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\Pages\Concerns\SyncsProductImages;
use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    use SyncsProductImages;

    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $this->syncProductImages();
    }
}