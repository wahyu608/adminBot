<?php

namespace App\Helpers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Cloudinary\Api\Upload\UploadApi;

class CloudinaryHelper
{
    public static function upload($file, ?string $folder = null): string
    {
        if ($file instanceof UploadedFile) {
            $filePath = $file->getRealPath();
        } elseif (is_string($file) && file_exists($file)) {
            $filePath = $file;
        } else {
            throw new \InvalidArgumentException('File tidak valid untuk upload');
        }

        $options = [];
        if ($folder) {
            $options['folder'] = $folder;
        }

        $uploaded = Cloudinary::upload($filePath, $options);

        return $uploaded->getSecurePath();
    }

    public static function deleteByUrl(?string $url): bool
    {
        if (!$url) return false;

        $publicId = self::getPublicIdFromUrl($url);
        if (!$publicId) return false;

        $result = (new UploadApi())->destroy($publicId);

        \Log::info('Cloudinary delete result', [
            'public_id' => $publicId,
            'response' => $result->getArrayCopy(),
        ]);

        return ($result['result'] ?? null) === 'ok';
    }

    public static function getPublicIdFromUrl(string $value): ?string
    {
        
        if (!str_starts_with($value, 'http')) {
            return preg_replace('/\.[^.]+$/', '', $value);
        }

        // Jika URL penuh Cloudinary
        $path = parse_url($value, PHP_URL_PATH);
        if (!$path) return null;

        $segments = explode('/upload/', $path);
        if (!isset($segments[1])) return null;

        $publicPath = preg_replace('/^v\d+\//', '', $segments[1]);

        return preg_replace('/\.[^.]+$/', '', $publicPath);
    }
}