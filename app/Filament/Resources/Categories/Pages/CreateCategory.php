<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Store the uploaded image path temporarily
        if (isset($data['image'])) {
            $this->imagePath = $data['image'];
            // Convert the storage path to a full URL for database storage
            $data['image'] = Storage::disk('public')->url($data['image']);
        }

        return $data;
    }

    protected ?string $imagePath = null;
}
