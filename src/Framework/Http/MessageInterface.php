<?php

declare(strict_types=1);

namespace App\Framework\Http;

interface MessageInterface
{
    public function getHeaders(): array;

    public function getHeader(string $name): ?string;

    public function withHeader(string $name, string $value): self;
}
