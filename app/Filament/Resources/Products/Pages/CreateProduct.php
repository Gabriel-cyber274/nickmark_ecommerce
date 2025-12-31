<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\ProductImage;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->images = $data['images'] ?? [];
        unset($data['images']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if (! empty($this->images)) {
            foreach ($this->images as $path) {
                ProductImage::create([
                    'product_id' => $this->record->id,
                    // ✅ convert path → full URL
                    'image_url' => Storage::disk('public')->url($path),
                ]);
            }
        }
    }

    protected array $images = [];
}
