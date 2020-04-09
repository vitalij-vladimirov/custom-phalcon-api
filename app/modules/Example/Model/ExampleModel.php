<?php
declare(strict_types=1);

namespace Example\Model;

use Common\BaseClasses\BaseModel;

class ExampleModel extends BaseModel
{
    public string $lib_name;
    public string $lib_url;
    public float $version;
    public string $description;

    public function getLibName(): string
    {
        return $this->lib_name;
    }

    public function setLibName(string $lib_name): ExampleModel
    {
        $this->lib_name = $lib_name;
        return $this;
    }

    public function getLibUrl(): string
    {
        return $this->lib_url;
    }

    public function setLibUrl(string $lib_url): ExampleModel
    {
        $this->lib_url = $lib_url;
        return $this;
    }

    public function getVersion(): float
    {
        return $this->version;
    }

    public function setVersion(float $version): ExampleModel
    {
        $this->version = $version;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): ExampleModel
    {
        $this->description = $description;
        return $this;
    }
}
