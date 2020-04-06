<?php
declare(strict_types=1);

namespace Common\Entity;

use Common\BaseClasses\BaseEntity;

class FileSizeEntity extends BaseEntity
{
    private int $bytes;
    private string $kilobytes;
    private string $megabytes;
    private string $gigabytes;

    public function getBytes(): int
    {
        return $this->bytes;
    }

    public function setBytes(int $bytes): FileSizeEntity
    {
        $this->bytes = $bytes;

        return $this;
    }

    public function getKilobytes(): string
    {
        return $this->kilobytes;
    }

    public function setKilobytes(string $kilobytes): FileSizeEntity
    {
        $this->kilobytes = $kilobytes;

        return $this;
    }

    public function getMegabytes(): string
    {
        return $this->megabytes;
    }

    public function setMegabytes(string $megabytes): FileSizeEntity
    {
        $this->megabytes = $megabytes;

        return $this;
    }

    public function getGigabytes(): string
    {
        return $this->gigabytes;
    }

    public function setGigabytes(string $gigabytes): FileSizeEntity
    {
        $this->gigabytes = $gigabytes;

        return $this;
    }
}
