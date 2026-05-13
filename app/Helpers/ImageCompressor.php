<?php

namespace App\Helpers;

class ImageCompressor
{
    /**
     * Compress an image file to be under the given max size in kilobytes.
     * Uses GD library (built-in with PHP). Returns the compressed image content as a string.
     *
     * @param  string  $filePath  Absolute path to the image file
     * @param  int  $maxSizeKb  Maximum file size in kilobytes (default 4096 = 4MB)
     * @return string  Compressed image content (JPEG)
     */
    public static function compress(string $filePath, int $maxSizeKb = 4096): string
    {
        $imageInfo = getimagesize($filePath);

        if ($imageInfo === false) {
            return file_get_contents($filePath);
        }

        $mime = $imageInfo['mime'];
        $image = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($filePath),
            'image/png' => imagecreatefrompng($filePath),
            'image/webp' => imagecreatefromwebp($filePath),
            default => null,
        };

        if ($image === null) {
            return file_get_contents($filePath);
        }

        // Resize if the image dimensions are excessively large
        $width = imagesx($image);
        $height = imagesy($image);
        $maxDimension = 2400;

        if ($width > $maxDimension || $height > $maxDimension) {
            $ratio = min($maxDimension / $width, $maxDimension / $height);
            $newWidth = (int) round($width * $ratio);
            $newHeight = (int) round($height * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        // Progressively lower JPEG quality until under max size
        $maxBytes = $maxSizeKb * 1024;
        $quality = 85;

        while ($quality >= 20) {
            ob_start();
            imagejpeg($image, null, $quality);
            $content = ob_get_clean();

            if (strlen($content) <= $maxBytes) {
                imagedestroy($image);

                return $content;
            }

            $quality -= 10;
        }

        // If still too large at quality 20, return the last attempt
        imagedestroy($image);

        return $content;
    }
}
