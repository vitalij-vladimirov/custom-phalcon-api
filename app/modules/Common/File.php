<?php
declare(strict_types=1);

namespace Common;

use Carbon\Carbon;
use Common\Config\ErrorCodes;
use Common\Entity\DirectoryEntity;
use Common\Entity\FileInfoEntity;
use Common\Entity\FileSizeEntity;
use Common\Exception\NotFoundException;

class File
{
    public static function write(string $file, string $text): void
    {
        self::validateFileDirectoryEntityAndCreateIfNotFound($file);

        file_put_contents($file, $text);
    }

    public static function read(string $file): ?string
    {
        if (!self::exists($file)) {
            return null;
        }

        return file_get_contents($file);
    }

    public static function exists(string $file): bool
    {
        return file_exists($file);
    }

    public static function getInfo(string $file): ?FileInfoEntity
    {
        if (!self::exists($file)) {
            return null;
        }

        $fileInfo = (new FileInfoEntity())
            ->setType(mime_content_type($file))
            ->setHash(md5_file($file))
            ->setLastModified(Carbon::createFromTimestamp(filemtime($file)))
            ->setFileSizeEntity(self::getSize($file))
            ->setDirectoryEntity(self::getDirectoryEntity($file))
        ;

        $pathSplitter = explode('/', $file);
        $fileInfo->setFullName($pathSplitter[count($pathSplitter)-1]);

        $fileNameSplitter = explode('.', $fileInfo->getFullName());
        $fileInfo->setExt($fileNameSplitter[count($fileNameSplitter)-1]);

        foreach ($fileNameSplitter as $namePart) {
            $name = !isset($name) ? $namePart : $name . '.' . $namePart;
        }
        $fileInfo->setName(substr($name, 0, (strlen($fileInfo->getExt()) + 1) * -1));

        return $fileInfo;
    }

    public static function getSize(string $file): ?FileSizeEntity
    {
        if (!self::exists($file)) {
            return null;
        }

        $bytes = filesize($file);

        return (new FileSizeEntity())
            ->setBytes($bytes)
            ->setKilobytes((string)number_format($bytes/1024, 2, '.', ''))
            ->setMegabytes((string)number_format($bytes/1024/1024, 2, '.', ''))
            ->setGigabytes((string)number_format($bytes/1024/1024/1024, 2, '.', ''))
        ;
    }

    public static function getDirectoryEntity(string $file): ?DirectoryEntity
    {
        $map = [];
        $pathSplitter = explode('/', $file);
        foreach ($pathSplitter as $location) {
            if (!empty($location)) {
                $map[] = $location;
            }
        }
        unset($map[count($map)-1]);

        $name = $map[count($map)-1];

        $path = '';
        foreach ($map as $location) {
            $path .= '/' . $location;
        }

        return (new DirectoryEntity())
            ->setName($name)
            ->setPath($path)
            ->setMap($map)
        ;
    }

    public static function validateFileExists(string $file): void
    {
        if (!self::exists($file)) {
            throw new NotFoundException('File not found', ErrorCodes::FILE_NOT_FOUND);
        }
    }

    private static function validateFileDirectoryEntityAndCreateIfNotFound(string $file): void
    {
        $directory = self::getDirectoryEntity($file);

        if (self::exists($directory->getPath())) {
            return;
        }

        $fullPath = '';
        for ($i = 0; $i < count($directory->getMap()); ++$i) {
            $fullPath .= '/' . $directory->getMap()[$i];

            if (self::exists($fullPath)) {
                continue;
            }

            mkdir($fullPath);
        }
    }
}
