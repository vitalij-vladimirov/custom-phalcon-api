<?php
declare(strict_types=1);

namespace Common\Entity;

use Carbon\Carbon;

class FileInfo
{
    private string $name;
    private string $ext;
    private string $fullName;
    private string $type;
    private string $hash;
    private Carbon $lastModified;
    private Directory $directory;
    private FileSize $fileSize;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): FileInfo
    {
        $this->name = $name;

        return $this;
    }

    public function getExt(): string
    {
        return $this->ext;
    }

    public function setExt(string $ext): FileInfo
    {
        $this->ext = $ext;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): FileInfo
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): FileInfo
    {
        $this->type = $type;

        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): FileInfo
    {
        $this->hash = $hash;

        return $this;
    }

    public function getLastModified(): Carbon
    {
        return $this->lastModified;
    }

    public function setLastModified(Carbon $lastModified): FileInfo
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getDirectory(): Directory
    {
        return $this->directory;
    }

    public function setDirectory(Directory $directory): FileInfo
    {
        $this->directory = $directory;

        return $this;
    }

    public function getFileSize(): FileSize
    {
        return $this->fileSize;
    }

    public function setFileSize(FileSize $fileSize): FileInfo
    {
        $this->fileSize = $fileSize;

        return $this;
    }
}
