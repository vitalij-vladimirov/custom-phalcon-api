<?php
declare(strict_types=1);

namespace Common\Entity;

class Directory
{
    private string $name;
    private string $path;
    private array $map;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Directory
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): Directory
    {
        $this->path = $path;

        return $this;
    }

    public function getMap(): array
    {
        return $this->map;
    }

    public function setMap(array $map): Directory
    {
        $this->map = $map;

        return $this;
    }
}
