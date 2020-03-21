<?php
declare(strict_types=1);

namespace Common\Service;

use Common\File;
use Common\Json;

class CacheManager
{
    public const NAMESPACES_CACHE_FILE = '/var/cache/phalcon/namespaces.json';

    private const MODULES_DIRECTORIES = [
        '/app/mvc' => 'BaseMvc',
        '/app/modules' => '',
    ];

    private const IGNORE_FILES = [
        '.',
        '..'
    ];

    public function cacheNamespaces(): void
    {
        $namespaces = [];

        foreach (self::MODULES_DIRECTORIES as $modulesDir => $namespaceBegin) {
            $modulesDirs = $this->generateDirectoriesList($modulesDir);

            if (!empty($namespaceBegin)) {
                $namespaces = array_merge($namespaces, [$namespaceBegin => $modulesDir]);
            }

            if (count($modulesDirs)) {
                $namespaces = array_merge(
                    $namespaces,
                    $this->generateNamespaceForDirectories($modulesDir, $namespaceBegin, $modulesDirs)
                );
            }
        }

        $namespacesJson = Json::encode($namespaces);

        $fileExists = File::exists(self::NAMESPACES_CACHE_FILE);
        if ($fileExists && File::getInfo(self::NAMESPACES_CACHE_FILE)->getHash() === md5($namespacesJson)) {
            return;
        }

        File::write(self::NAMESPACES_CACHE_FILE, $namespacesJson);
    }

    private function generateDirectoriesList(string $directory): array
    {
        $directories = $this->searchForDirectories($directory);

        if (count($directories)) {
            $subDirectories = $directories;
            foreach ($subDirectories as $directory) {
                $directories = array_merge(
                    $directories,
                    $this->searchForDirectories($directory)
                );
            }
        }

        return $directories;
    }

    private function searchForDirectories(string $directory): array
    {
        $directories = [];

        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                $path = $directory . '/' . $file;

                if (!in_array($file, self::IGNORE_FILES, true) && filetype($path) === 'dir') {
                    $directories[] = $path;
                }
            }
            closedir($handle);
        }

        return $directories;
    }

    private function generateNamespaceForDirectories(
        string $modulesDir,
        string $namespaceBegin,
        array $directories
    ): array {
        $namespaces = [];

        foreach ($directories as $directory) {
            $namespace = substr($directory, strlen($modulesDir) + 1);
            $namespace = str_replace('/', '\\', $namespace);

            if (!empty($namespaceBegin)) {
                $namespace = $namespaceBegin . '\\' . $namespace;
            }

            $namespaces[$namespace] = $directory;
        }

        return $namespaces;
    }
}
