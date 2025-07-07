<?php

namespace App\Cache;

class CacheManager
{
    private $cacheDir;
    private $defaultTtl;

    public function __construct(string $cacheDir, int $defaultTtl = 3600) // Default TTL: 1 hour
    {
        $this->cacheDir = $cacheDir;
        $this->defaultTtl = $defaultTtl;
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0775, true);
        }
    }

    private function getCacheFilePath(string $key): string
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . md5($key) . ".cache";
    }

    public function set(string $key, $data, int $ttl = null): bool
    {
        $ttl = $ttl ?? $this->defaultTtl;
        $expiresAt = time() + $ttl;
        $cacheData = [
            "expires_at" => $expiresAt,
            "data" => $data,
        ];
        return file_put_contents($this->getCacheFilePath($key), serialize($cacheData)) !== false;
    }

    public function get(string $key)
    {
        $filePath = $this->getCacheFilePath($key);
        if (file_exists($filePath)) {
            $cachedContent = file_get_contents($filePath);
            $cacheData = unserialize($cachedContent);

            if ($cacheData && isset($cacheData["expires_at"]) && $cacheData["expires_at"] > time()) {
                return $cacheData["data"];
            } else {
                // Cache expired or invalid, delete it
                unlink($filePath);
            }
        }
        return null;
    }

    public function clear(string $key): bool
    {
        $filePath = $this->getCacheFilePath($key);
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }

    public function clearAll(): void
    {
        $files = glob($this->cacheDir . DIRECTORY_SEPARATOR . "*.cache");
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}


