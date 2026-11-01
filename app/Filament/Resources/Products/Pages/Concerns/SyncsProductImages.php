<?php

namespace App\Filament\Resources\Products\Pages\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait SyncsProductImages
{
    protected function syncProductImages(): void
    {
        $product = $this->record;
        $state   = collect($this->form->getRawState()['images'] ?? []);

        // existing images the user kept (strings) vs. brand-new uploads
        $kept = $state->filter(fn ($f) => is_string($f))->values();
        $new  = $state->filter(fn ($f) => $f instanceof TemporaryUploadedFile || $f instanceof UploadedFile);

        // remove images that were deleted in the form
        foreach ($product->images()->get() as $image) {
            if (! $kept->contains($image->image_url)) {
                if (! Str::startsWith($image->image_url, ['http://', 'https://'])) {
                    Storage::disk('public')->delete($image->image_url);
                }
                $image->delete();
            }
        }

        // store new uploads
        foreach ($new as $file) {
            $product->images()->create([
                'image_url' => $file->store('products', 'public'),
            ]);
        }
    }
}