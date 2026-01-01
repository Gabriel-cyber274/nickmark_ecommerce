<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert the full URL back to storage path for the FileUpload component
        if (isset($data['image']) && $data['image']) {
            $data['image'] = str_replace(
                Storage::disk('public')->url(''),
                '',
                $data['image']
            );
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store old image path for deletion
        if ($this->record->image) {
            $this->oldImagePath = str_replace(
                Storage::disk('public')->url(''),
                '',
                $this->record->image
            );
        }

        // Handle new image upload
        if (isset($data['image']) && $data['image']) {
            $this->newImagePath = $data['image'];
            // Convert storage path to full URL for database
            $data['image'] = Storage::disk('public')->url($data['image']);
        } else {
            // If no new image, keep the existing image URL
            $data['image'] = $this->record->image;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Delete old image if a new one was uploaded and old one exists
        if ($this->newImagePath && $this->oldImagePath && $this->oldImagePath !== $this->newImagePath) {
            if (Storage::disk('public')->exists($this->oldImagePath)) {
                Storage::disk('public')->delete($this->oldImagePath);
            }
        }
    }

    protected ?string $oldImagePath = null;
    protected ?string $newImagePath = null;
}
