<?php
declare(strict_types=1);

namespace Documentation\Entity;

use Common\BaseClasses\BaseEntity;

class RouteDoc extends BaseEntity
{
    private bool $status;
    private ?string $summary = null;
    private ?string $description = null;
    private array $exceptionsList;

    /**
     * If documentation status is set to false (disabled) mandatory fields
     * should be filled with some default values to avoid Exceptions.
     *
     * RouteDocs constructor.
     * @param bool $status
     */
    public function __construct(bool $status = true)
    {
        $this->status = $status;

        if (!$status) {
            $this->setExceptionsList([]);
        }
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): RouteDoc
    {
        $this->summary = $summary;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): RouteDoc
    {
        $this->description = $description;
        return $this;
    }

    public function getExceptionsList(): array
    {
        return $this->exceptionsList;
    }

    public function setExceptionsList(array $exceptionsList): RouteDoc
    {
        $this->exceptionsList = $exceptionsList;
        return $this;
    }
}
