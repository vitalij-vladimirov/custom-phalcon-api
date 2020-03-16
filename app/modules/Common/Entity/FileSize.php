<?php
declare(strict_types=1);

namespace Common\Entity;

class FileSize
{
    private int $bytes;
    private string $kilobytes;
    private string $megabytes;
    private string $gigabytes;

    public function getBytes(): int
    {
        return $this->bytes;
    }

    public function setBytes(int $bytes): FileSize
    {
        $this->bytes = $bytes;

        return $this;
    }

    public function getKilobytes(): string
    {
        return $this->kilobytes;
    }

    public function setKilobytes(string $kilobytes): FileSize
    {
        $this->kilobytes = $kilobytes;

        return $this;
    }

    public function getMegabytes(): string
    {
        return $this->megabytes;
    }

    public function setMegabytes(string $megabytes): FileSize
    {
        $this->megabytes = $megabytes;

        return $this;
    }

    public function getGigabytes(): string
    {
        return $this->gigabytes;
    }

    public function setGigabytes(string $gigabytes): FileSize
    {
        $this->gigabytes = $gigabytes;

        return $this;
    }
}