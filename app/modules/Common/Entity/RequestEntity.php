<?php
declare(strict_types=1);

namespace Common\Entity;

use Common\BaseClasses\BaseEntity;

class RequestEntity extends BaseEntity
{
    public const REQUEST_TYPE_API = 'api';
    public const REQUEST_TYPE_VIEW = 'view';

    private string $method;
    private string $type;
    private string $path;
    private string $module;
    private array $params;
    private array $query;

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): RequestEntity
    {
        $this->method = $method;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): RequestEntity
    {
        $this->type = $type;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): RequestEntity
    {
        $this->path = $path;
        return $this;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function setModule(string $module): RequestEntity
    {
        $this->module = $module;
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): RequestEntity
    {
        $this->params = $params;
        return $this;
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function setQuery(array $query): RequestEntity
    {
        $this->query = $query;
        return $this;
    }
}
