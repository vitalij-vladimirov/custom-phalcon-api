<?php
declare(strict_types=1);

namespace Common\Entity;

use Carbon\Carbon;
use Common\BaseClasses\BaseEntity;

class FileInfoEntity extends BaseEntity
{
    private string $name;
    private string $ext;
    private string $fullName;
    private string $type;
    private string $hash;
    private Carbon $lastModified;
    private DirectoryEntity $directory;
    private FileSizeEntity $fileSize;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): FileInfoEntity
    {
        $this->name = $name;

        return $this;
    }

    public function getExt(): string
    {
        return $this->ext;
    }

    public function setExt(string $ext): FileInfoEntity
    {
        $this->ext = $ext;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): FileInfoEntity
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): FileInfoEntity
    {
        $this->type = $type;

        return $this;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): FileInfoEntity
    {
        $this->hash = $hash;

        return $this;
    }

    public function getLastModified(): Carbon
    {
        return $this->lastModified;
    }

    public function setLastModified(Carbon $lastModified): FileInfoEntity
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function getDirectory(): DirectoryEntity
    {
        return $this->directory;
    }

    public function setDirectory(DirectoryEntity $directory): FileInfoEntity
    {
        $this->directory = $directory;

        return $this;
    }

    public function getFileSize(): FileSizeEntity
    {
        return $this->fileSize;
    }

    public function setFileSize(FileSizeEntity $fileSize): FileInfoEntity
    {
        $this->fileSize = $fileSize;

        return $this;
    }
}
