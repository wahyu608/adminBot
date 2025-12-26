<?php

namespace App\Helpers;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryHelper
{
    /**
     * Upload file ke Cloudinary
     *
     * @param string $filePath
     * @param string|null $folder
     * @return string URL secure
     */
    public static function upload(string $filePath, ?string $folder = null): string
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File tidak ditemukan: $filePath");
        }

        $options = [];
        if ($folder) {
            $options['folder'] = $folder;
        }

        $uploaded = Cloudinary::upload($filePath, $options);

        return $uploaded->getSecurePath();
    }

    /**
     * Hapus file dari Cloudinary
     *
     * @param string $url Cloudinary URL
     * @return array
     */
    public static function deleteByUrl(string $url): array
    {
        $publicId = self::getPublicIdFromUrl($url);
        if (!$publicId) return [];
        return Cloudinary::destroy($publicId);
    }

    /**
     * Ambil public_id dari URL Cloudinary
     *
     * @param string $url
     * @return string|null
     */
    public static function getPublicIdFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH); // /image/upload/v123/folder/file.jpg
        $parts = explode('/', $path);
        $filename = end($parts); // file.jpg
        $publicId = pathinfo($filename, PATHINFO_FILENAME); // file
        return $publicId ?: null;
    }
}
