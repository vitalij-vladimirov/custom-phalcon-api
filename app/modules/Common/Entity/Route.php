<?php
declare(strict_types=1);

namespace Common\Entity;

use Common\BaseClasses\BaseEntity;
use Common\Exception\LogicException;
use Documentation\Entity\RouteDoc;

class Route extends BaseEntity
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_MOVED = 'moved';
    public const STATUS_DEPRECATED = 'deprecated';

    private const ALLOWED_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_MOVED,
        self::STATUS_DEPRECATED,
    ];

    private string $status = self::STATUS_ACTIVE;
    private bool $isSecured = true;
    private string $controller;
    private string $action;
    private array $permissions;
    private ?string $validator = null;
    private array $resolvers = [];
    private ?string $requestMapper = null;
    private ?string $responseMapper = null;
    private RouteDoc $documentation;

    public function __construct(bool $documentation = true)
    {
        if (!$documentation) {
            $this->setDocumentation(new RouteDoc(false));
        }
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Route
    {
        if (!in_array($status, self::ALLOWED_STATUSES, true)) {
            throw new LogicException('You are trying to assign route status that is not allowed.');
        }

        $this->status = $status;
        return $this;
    }

    public function isSecured(): bool
    {
        return $this->isSecured;
    }

    public function setIsSecured(bool $isSecured): Route
    {
        $this->isSecured = $isSecured;
        return $this;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setController(string $controller): Route
    {
        $this->controller = $controller;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): Route
    {
        $this->action = $action;
        return $this;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): Route
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function getValidator(): ?string
    {
        return $this->validator;
    }

    public function setValidator(?string $validator): Route
    {
        $this->validator = $validator;
        return $this;
    }

    public function getResolvers(): array
    {
        return $this->resolvers;
    }

    public function setResolvers(array $resolvers): Route
    {
        $this->resolvers = $resolvers;
        return $this;
    }

    public function getRequestMapper(): ?string
    {
        return $this->requestMapper;
    }

    public function setRequestMapper(?string $requestMapper): Route
    {
        $this->requestMapper = $requestMapper;
        return $this;
    }

    public function getResponseMapper(): ?string
    {
        return $this->responseMapper;
    }

    public function setResponseMapper(?string $responseMapper): Route
    {
        $this->responseMapper = $responseMapper;
        return $this;
    }

    public function getDocumentation(): RouteDoc
    {
        return $this->documentation;
    }

    public function setDocumentation(RouteDoc $documentation): Route
    {
        $this->documentation = $documentation;
        return $this;
    }
}
