<?php
declare(strict_types=1);

namespace Common\Entity;

use Common\BaseClasses\BaseEntity;

class DirectoryData extends BaseEntity
{
    private string $name;
    private string $path;
    private array $map;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): DirectoryData
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): DirectoryData
    {
        $this->path = $path;

        return $this;
    }

    public function getMap(): array
    {
        return $this->map;
    }

    public function setMap(array $map): DirectoryData
    {
        $this->map = $map;

        return $this;
    }
}
