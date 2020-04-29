<?php
declare(strict_types=1);

namespace Common\Entity;

use Carbon\Carbon;
use Common\BaseClass\BaseEntity;

class FileData extends BaseEntity
{
    private string $name;
    private string $ext;
    private string $fullName;
    private string $type;
    private string $hash;
    private Carbon $lastModified;
    private DirectoryData $directory;
    private FileSizeData $fileSize;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): FileData
    {
        $this->name = $name;

        return $this;
    }

    public function getExt(): string
    {
        return $this->ext;
    }

    public function setExt(string $ext): FileData
    {
        $this->ext = $ext;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): FileData
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): FileData
    {
        $this->type = $type;

        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): FileData
    {
        $this->hash = $hash;

        return $this;
    }

    public function getLastModified(): Carbon
    {
        return $this->lastModified;
    }

    public function setLastModified(Carbon $lastModified): FileData
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getDirectory(): DirectoryData
    {
        return $this->directory;
    }

    public function setDirectory(DirectoryData $directory): FileData
    {
        $this->directory = $directory;

        return $this;
    }

    public function getFileSize(): FileSizeData
    {
        return $this->fileSize;
    }

    public function setFileSize(FileSizeData $fileSize): FileData
    {
        $this->fileSize = $fileSize;

        return $this;
    }
}
