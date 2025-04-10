<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class ImageUploader
{
    /**
     * Upload and optimize image in WebP format.
     *
     * @param UploadedFile $file
     * @param string $folder - e.g., "products", "categories"
     * @param int|null $maxWidth
     * @return string|null
     */
    public static function upload(UploadedFile $file, string $folder, int $maxWidth = 1200): ?string
    {
        try {
            // Generate unique WebP filename
            $filename = uniqid() . '_' . time() . '.webp';
            $folder = trim($folder, '/');
            $storagePath = "uploads/{$folder}/{$filename}";

            // Create new ImageManager instance with Imagick driver
            $manager = new ImageManager(new Driver());

            // Process the image
            $image = $manager->read($file->getPathname());

            // Resize if width exceeds maximum while maintaining aspect ratio
            if ($image->width() > $maxWidth) {
                $image->scaleDown(width: $maxWidth);
            }

            // Convert to WebP with quality optimization (0-100)
            $webpQuality = 80; // Adjust quality as needed (higher = better quality but larger file)
            $encodedImage = $image->toWebp($webpQuality);

            // Ensure the directory exists
            Storage::disk('public')->makeDirectory("uploads/{$folder}");

            // Save the optimized image
            Storage::disk('public')->put($storagePath, $encodedImage->toString());

            // Generate public URL path
            $publicPath = str_replace('public/', '/storage/', $storagePath);

            // Return the storage path for the uploaded image
            return $publicPath;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    /**
     * Delete uploaded image.
     *
     * @param string $path
     * @return bool
     */
    public static function delete(string $path): bool
    {
        try {
            if (!Storage::disk('public')->exists($path)) {
                return false; // File doesn't exist
            }
            return Storage::disk('public')->delete($path);
        } catch (\Exception $e) {
            report($e);
            return false;
        }
    }
}
