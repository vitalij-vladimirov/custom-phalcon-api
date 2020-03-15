<?php
declare(strict_types=1);

namespace Common\Task;

use Phalcon\Cli\Task;
use Common\{Cache, Json};

class CacheTask extends Task
{
    private const MODULES_DIRECTORIES = [
        '/app/modules' => '',
    ];

    private const IGNORE_FILES = [
        '.',
        '..'
    ];

    /**
     * Run namespace caching every 15 seconds
     */
    public function cacheNamespacesAction()
    {
        $namespaces = [];

        for ($i = 1; $i <= 4; ++$i) {
            foreach (self::MODULES_DIRECTORIES as $directory => $namespace) {
                $directories = $this->generateDirectoriesList($directory);

                if (count($directories)) {
                    $namespaces = array_merge(
                        $namespaces,
                        $this->generateNamespaceForDirectories($directory, $namespace, $directories)
                    );
                }
            }

            Cache::write('namespaces', Json::encode($namespaces));

            sleep(15);
        }
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
                if (
                    !in_array($file, self::IGNORE_FILES, true)
                    && filetype($path) === 'dir'
                ) {
                    $directories[] = $path;
                }
            }
            closedir($handle);
        }

        return $directories;
    }

    private function generateNamespaceForDirectories(
        string $moduleDirectory,
        string $moduleNamespace,
        array $directories
    ): array {
        $namespaces = [];

        foreach ($directories as $directory) {
            $namespace = substr($directory, strlen($moduleDirectory) + 1);
            $namespace = str_replace('/', '\\', $namespace);

            if (!empty($moduleNamespace)) {
                $namespace = $moduleNamespace . '\\' . $namespace;
            }

            $namespaces[$namespace] = $directory;
        }

        return $namespaces;
    }
}