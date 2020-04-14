<?php
declare(strict_types=1);

namespace Common\Service;

use Common\BaseClasses\Injectable;
use Common\File;
use Common\Json;
use Phalcon\Config;

final class CacheManager extends Injectable
{
    private const IGNORE_FILES = [
        '.',
        '..',
    ];

    private Config $config;
    private array $modulesDirectories;

    public function __construct()
    {
        $this->config = $this->di->get('config');

        $this->modulesDirectories = [
            $this->config->application->mvcDir => 'Mvc',
            $this->config->application->seedsDir => 'Seeds',
            $this->config->application->modulesDir => '',
        ];
    }

    public function cacheNamespaces(): void
    {
        $namespaces = [];

        foreach ($this->modulesDirectories as $modulesDir => $namespaceBegin) {
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

        if (File::exists($this->config->application->namespacesCache)
            && File::getInfo($this->config->application->namespacesCache)->getHash() === md5($namespacesJson)
        ) {
            return;
        }

        File::write($this->config->application->namespacesCache, $namespacesJson);
    }

    private function generateDirectoriesList(string $directory): array
    {
        $directories = [];
        $handle = opendir($directory);

        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                $path = $directory . '/' . $file;

                if (!in_array($file, self::IGNORE_FILES, true) && filetype($path) === 'dir') {
                    $directories[] = $path;

                    $subDirectories = $this->generateDirectoriesList($path);
                    if (count($subDirectories)) {
                        $directories = array_merge($directories, $subDirectories);
                    }
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
