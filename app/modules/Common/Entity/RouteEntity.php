<?php
declare(strict_types=1);

namespace Common\Entity;

use Common\BaseClasses\BaseEntity;

class RouteEntity extends BaseEntity
{
    private string $controller;
    private string $action;
    private array $scopes = [];
    private array $resolvers = [];
    private ?string $requestMapper;
    private ?string $validator;
    private ?string $responseMapper;

    public function getController(): string
    {
        return $this->controller;
    }

    public function setController(string $controller): RouteEntity
    {
        $this->controller = $controller;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): RouteEntity
    {
        $this->action = $action;
        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): RouteEntity
    {
        $this->scopes = $scopes;
        return $this;
    }

    public function getResolvers(): array
    {
        return $this->resolvers;
    }

    public function setResolvers(array $resolvers): RouteEntity
    {
        $this->resolvers = $resolvers;
        return $this;
    }

    public function getRequestMapper(): ?string
    {
        return $this->requestMapper;
    }

    public function setRequestMapper(?string $requestMapper): RouteEntity
    {
        $this->requestMapper = $requestMapper;
        return $this;
    }

    public function getValidator(): ?string
    {
        return $this->validator;
    }

    public function setValidator(?string $validator): RouteEntity
    {
        $this->validator = $validator;
        return $this;
    }

    public function getResponseMapper(): ?string
    {
        return $this->responseMapper;
    }

    public function setResponseMapper(?string $responseMapper): RouteEntity
    {
        $this->responseMapper = $responseMapper;
        return $this;
    }
}
