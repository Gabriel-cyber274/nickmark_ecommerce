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

        // paths/urls Filament says should exist after this save
        $kept = $state->filter(fn ($f) => is_string($f))
            ->map(fn ($f) => $this->localImagePath($f) ?? $f)
            ->unique()
            ->values();

        $new = $state->filter(fn ($f) => $f instanceof TemporaryUploadedFile || $f instanceof UploadedFile);

        $images = $product->images()->get();

        // 1. delete removed/replaced images (DB row + file on disk)
        foreach ($images as $image) {
            $raw  = $image->getRawOriginal('image_url');
            $path = $this->localImagePath($raw);
            $key  = $path ?? $raw;

            if (! $kept->contains($key)) {
                if ($path && Str::startsWith($path, 'products/')) {
                    Storage::disk('public')->delete($path);
                }
                $image->delete();
            }
        }

        // 2. add paths Filament stored that aren't in the DB yet
        $existing = $product->images()->get()
            ->map(fn ($i) => $this->localImagePath($i->getRawOriginal('image_url')) ?? $i->getRawOriginal('image_url'));

        foreach ($kept->diff($existing) as $path) {
            $product->images()->create(['image_url' => $path]);
        }

        // 3. raw uploads Filament didn't store itself
        foreach ($new as $file) {
            $product->images()->create([
                'image_url' => $file->store('products', 'public'),
            ]);
        }
    }

    /**
     * Returns the path on the public disk, or null if it's an external URL.
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
                : null; // external URL, never touch
        }

        return ltrim($value, '/');
    }
}
