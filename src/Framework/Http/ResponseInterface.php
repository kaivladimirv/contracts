<?php

declare(strict_types=1);

namespace App\Framework\Http;

interface ResponseInterface extends MessageInterface
{
    public function send(): void;

    public function getStatusCode(): int;

    public function withStatus(int $code, string $reasonPhrase = ''): self;

    public function getReasonPhrase(): string;
}
