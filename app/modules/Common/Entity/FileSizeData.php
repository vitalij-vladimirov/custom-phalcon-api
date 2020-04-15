<?php
declare(strict_types=1);

namespace Common\Entity;

use Common\BaseClasses\BaseEntity;

class FileSizeData extends BaseEntity
{
    private int $bytes;
    private string $kilobytes;
    private string $megabytes;
    private string $gigabytes;

    public function getBytes(): int
    {
        return $this->bytes;
    }

    public function setBytes(int $bytes): FileSizeData
    {
        $this->bytes = $bytes;

        return $this;
    }

    public function getKilobytes(): string
    {
        return $this->kilobytes;
    }

    public function setKilobytes(string $kilobytes): FileSizeData
    {
        $this->kilobytes = $kilobytes;

        return $this;
    }

    public function getMegabytes(): string
    {
        return $this->megabytes;
    }

    public function setMegabytes(string $megabytes): FileSizeData
    {
        $this->megabytes = $megabytes;

        return $this;
    }

    public function getGigabytes(): string
    {
        return $this->gigabytes;
    }

    public function setGigabytes(string $gigabytes): FileSizeData
    {
        $this->gigabytes = $gigabytes;

        return $this;
    }
}
