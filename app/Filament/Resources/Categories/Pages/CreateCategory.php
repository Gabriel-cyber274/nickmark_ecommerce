<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function afterCreate(): void
    {
        $image = $this->form->getState()['image'] ?? null;

        if ($image instanceof TemporaryUploadedFile) {
            $path = $image->store('categories', 'public');

            $this->record->update([
                'image' => Storage::disk('public')->url($path),
            ]);
        }
    }
}
