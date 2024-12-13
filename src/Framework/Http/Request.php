<?php

declare(strict_types=1);

namespace App\Framework\Http;

use Override;

class Request implements ServerRequestInterface
{
    private array $attributes = [];
    private array $headers = [];

    public function __construct()
    {
        $this->fillHeaders();
    }

    #[Override]
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    #[Override]
    public function getUriPath(): string
    {
        return parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    #[Override]
    public function getQueryParam($paramName): array|string
    {
        return $_GET[$paramName] ?? '';
    }

    #[Override]
    public function getPostParam($paramName): array|string
    {
        return $_POST[$paramName] ?? '';
    }

    #[Override]
    public function getServerParam($paramName): array|string
    {
        return $_SERVER[$paramName] ?? '';
    }

    #[Override]
    public function withAttributes(array $attrs): self
    {
        $clone = clone $this;
        $clone->attributes = array_merge($this->attributes, $attrs);

        return $clone;
    }

    #[Override]
    public function withAttribute($name, $value): self
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    #[Override]
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    #[Override]
    public function getAttribute($name, $default = null): mixed
    {
        return (array_key_exists($name, $this->attributes) !== false ? $this->attributes[$name] : $default);
    }

    #[Override]
    public function getHeaders(): array
    {
        return $this->headers;
    }

    private function fillHeaders(): void
    {
        $this->headers = [];

        foreach (getallheaders() as $name => $value) {
            $this->addHeader($name, $value);
        }
    }

    private function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    #[Override]
    public function getHeader($name): ?string
    {
        return (array_key_exists($name, $this->headers) !== false ? $this->headers[$name] : null);
    }

    #[Override]
    public function withHeader($name, $value): self
    {
        $clone = clone $this;
        $clone->headers[$name] = $value;

        return $clone;
    }
}
