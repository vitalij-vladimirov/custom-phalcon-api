<?php
declare(strict_types=1);

namespace Common;

class Cache
{
    private const CACHE_DIRECTORY = '/var/cache/api';

    public static function write(string $cacheFile, string $cacheText): void
    {
        self::validateCacheDirectory();

        file_put_contents(self::getFilePath($cacheFile), $cacheText);
    }

    public static function read(string $cacheFile): ?string
    {
        $file = self::getFilePath($cacheFile);

        if (!file_exists($file)) {
            return null;
        }

        return file_get_contents($file);
    }

    private static function validateCacheDirectory(): void
    {
        if (file_exists(self::CACHE_DIRECTORY)) {
            return;
        }

        $pathPart = explode('/', self::CACHE_DIRECTORY);
        $fullPath = '';
        for ($i = 1; $i < count($pathPart); ++$i) {
            $fullPath .= '/' . $pathPart[$i];
            if (file_exists($fullPath)) {
                continue;
            }

            mkdir($fullPath);
        }
    }

    private static function getFilePath(string $file): ?string
    {
        return self::CACHE_DIRECTORY . '/' . $file . '.txt';
    }
}