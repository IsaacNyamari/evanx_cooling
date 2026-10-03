<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Images live in public/uploads so they work on shared hosting without a storage symlink.
 * Anything missing on disk falls back to a placeholder, so pages never show broken images.
 */
trait HasImages
{
    public static function resolveImage(?string $path): string
    {
        if (blank($path)) {
            return asset('img/placeholder.svg');
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return is_file(public_path('uploads/'.$path))
            ? asset('uploads/'.$path)
            : asset('img/placeholder.svg');
    }

    public function imageUrl(): string
    {
        return static::resolveImage($this->image);
    }

    public function hasImage(): bool
    {
        return filled($this->image) && $this->imageUrl() !== asset('img/placeholder.svg');
    }
}
