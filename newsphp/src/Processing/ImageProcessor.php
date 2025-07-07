<?php

namespace App\Processing;

use App\DataSource\HttpClient;

class ImageProcessor
{
    private $httpClient;
    private $imageCacheDir;

    public function __construct(HttpClient $httpClient, string $imageCacheDir)
    {
        $this->httpClient = $httpClient;
        $this->imageCacheDir = $imageCacheDir;
        if (!is_dir($this->imageCacheDir)) {
            mkdir($this->imageCacheDir, 0775, true);
        }
    }

    public function processImage(string $imageUrl, int $width = null, int $height = null): ?string
    {
        $imageContent = $this->httpClient->get($imageUrl);
        if (!$imageContent) {
            return null;
        }

        $imageHash = md5($imageUrl);
        $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
        $localPath = $this->imageCacheDir . DIRECTORY_SEPARATOR . $imageHash . "." . $extension;

        if (file_put_contents($localPath, $imageContent) === false) {
            return null; // Failed to save image locally
        }

        // Basic resizing (requires GD library)
        if (($width || $height) && function_exists("imagecreatefromstring")) {
            $image = imagecreatefromstring($imageContent);
            if ($image) {
                $originalWidth = imagesx($image);
                $originalHeight = imagesy($image);

                if (!$width && $height) {
                    $width = ($originalWidth / $originalHeight) * $height;
                } elseif ($width && !$height) {
                    $height = ($originalHeight / $originalWidth) * $width;
                } elseif (!$width && !$height) {
                    $width = $originalWidth;
                    $height = $originalHeight;
                }

                $newImage = imagecreatetruecolor($width, $height);
                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

                // Save resized image, overwriting original local file
                switch (strtolower($extension)) {
                    case "jpeg":
                    case "jpg":
                        imagejpeg($newImage, $localPath);
                        break;
                    case "png":
                        imagepng($newImage, $localPath);
                        break;
                    case "gif":
                        imagegif($newImage, $localPath);
                        break;
                    default:
                        // Unsupported format, keep original downloaded image
                        break;
                }
                imagedestroy($image);
                imagedestroy($newImage);
            }
        }

        return $localPath; // Return local path to the processed image
    }
}


