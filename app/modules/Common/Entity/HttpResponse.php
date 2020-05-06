<?php
declare(strict_types=1);

namespace Common\Entity;

use Common\BaseClass\BaseEntity;

class HttpResponse extends BaseEntity
{
    private int $statusCode;
    private string $contentType;
    private string $content;
    private array $jsonContent = [];
    private int $size;
    private array $headers = [];
    private ?string $responsePhrase;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): HttpResponse
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): HttpResponse
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): HttpResponse
    {
        $this->content = $content;
        return $this;
    }

    public function getJsonContent(): array
    {
        return $this->jsonContent;
    }

    public function setJsonContent(array $jsonContent): HttpResponse
    {
        $this->jsonContent = $jsonContent;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): HttpResponse
    {
        $this->size = $size;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): HttpResponse
    {
        $this->headers = $headers;
        return $this;
    }

    public function getResponsePhrase(): ?string
    {
        return $this->responsePhrase;
    }

    public function setResponsePhrase(?string $responsePhrase): HttpResponse
    {
        $this->responsePhrase = $responsePhrase;
        return $this;
    }
}
