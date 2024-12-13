<?php

declare(strict_types=1);

namespace App\Framework\Http;

interface ServerRequestInterface extends RequestInterface
{
    public function getQueryParam(string $paramName): array|string;

    public function getPostParam(string $paramName): array|string;

    public function getServerParam(string $paramName): array|string;

    public function withAttributes(array $attrs): self;

    public function withAttribute(string $name, mixed $value): self;

    public function getAttribute(string $name, mixed $default = null): mixed;

    public function getAttributes(): array;
}
