<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    protected function afterSave(): void
    {
        $image = $this->form->getState()['image'] ?? null;
        $record = $this->record;
        $old = $record->image;

        // Only act when a new image was uploaded to replace the existing one.
        // The image column is NOT NULL, so never set it to null.
        if (! $image instanceof UploadedFile) {
            return;
        }

        $path = $image->store('categories', 'public');
        $url = Storage::disk('public')->url($path);

        $record->update(['image' => $url]);

        $this->deleteLocalImage($old);
    }

    protected function deleteLocalImage(?string $url): void
    {
        $path = $this->localImagePath($url);

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Returns the path on the public disk, or null if it's blank or an external URL.
     */
    protected function localImagePath(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            $base = rtrim(Storage::disk('public')->url(''), '/') . '/';

            return Str::startsWith($value, $base)
                ? urldecode(Str::after($value, $base))
                : null;
        }

        return ltrim($value, '/');
    }
}
