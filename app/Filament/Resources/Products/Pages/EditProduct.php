<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\ProductImage;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['images'] = $this->record->images
            ->pluck('image_url')
            ->map(fn($url) => str_replace(Storage::disk('public')->url(''), '', $url))
            ->toArray();

        return $data;
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store images separately and remove from main data
        $images = $data['images'] ?? [];
        unset($data['images']);

        // Store images in a property to handle after product update
        $this->images = $images;

        return $data;
    }


    protected function afterSave(): void
    {
        foreach ($this->record->images as $image) {
            // Convert full URL back to storage path
            $path = str_replace(
                Storage::disk('public')->url(''),
                '',
                $image->image_url
            );

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Remove old images
        $this->record->images()->delete();

        if (!empty($this->images)) {
            foreach ($this->images as $path) {
                ProductImage::create([
                    'product_id' => $this->record->id,
                    'image_url' => Storage::disk('public')->url($path),
                ]);
            }
        }
    }

    protected array $images = [];
}
