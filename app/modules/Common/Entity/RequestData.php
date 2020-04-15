<?php
declare(strict_types=1);

namespace Common\Entity;

use Common\BaseClasses\BaseEntity;

class RequestData extends BaseEntity
{
    public const REQUEST_TYPE_API = 'api';
    public const REQUEST_TYPE_VIEW = 'view';

    private string $method;
    private string $type;
    private string $path;
    private string $module;
    private array $params;
    private array $data;

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): RequestData
    {
        $this->method = $method;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): RequestData
    {
        $this->type = $type;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): RequestData
    {
        $this->path = $path;
        return $this;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function setModule(string $module): RequestData
    {
        $this->module = $module;
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param int $key
     * @return string|int|float
     */
    public function getParam(int $key)
    {
        return $this->params[$key];
    }

    public function setParams(array $params): RequestData
    {
        $this->params = $params;
        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): RequestData
    {
        $this->data = $data;
        return $this;
    }
}
