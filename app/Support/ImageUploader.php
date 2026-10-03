<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Saves an uploaded image into public/uploads/{dir} and proves it landed on disk.
 * Throws a RuntimeException with a human-readable message on any failure, so callers
 * can abort before touching the database (no record ever points at a missing file).
 */
class ImageUploader
{
    public static function store(UploadedFile $file, string $dir): string
    {
        $root = public_path('uploads');
        $target = $root.DIRECTORY_SEPARATOR.$dir;

        File::ensureDirectoryExists($target, 0755, true);

        if (! is_dir($target) || ! is_writable($target)) {
            throw new RuntimeException("The upload folder (public/uploads/{$dir}) is not writable. Set its permissions to 755 or 775 in cPanel.");
        }

        $extension = $file->guessExtension() ?: 'jpg';
        $name = Str::lower(Str::random(24)).'.'.$extension;
        $path = $dir.'/'.$name;

        $stream = @fopen($file->getRealPath(), 'rb');
        if ($stream === false) {
            throw new RuntimeException('The uploaded file could not be read. Please try again.');
        }

        try {
            $saved = Storage::disk('uploads')->put($path, $stream);
        } finally {
            fclose($stream);
        }

        if (! $saved || ! is_file($root.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$name) || filesize($root.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$name) === 0) {
            throw new RuntimeException('The image could not be saved to the server. Please try again or contact your host.');
        }

        return $path;
    }
}
