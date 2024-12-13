<?php

declare(strict_types=1);

namespace App\Framework\Http;

use Override;

class Response implements ResponseInterface
{
    private string $reasonPhrase = '';
    private array $headers = [];

    public function __construct(private int $statusCode, private readonly string $content)
    {
    }

    protected function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    #[Override]
    public function send(): void
    {
        $this->sendHeaders();
        http_response_code($this->statusCode);

        echo $this->content;
    }

    private function sendHeaders(): void
    {
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value, false, $this->statusCode);
        }
    }

    #[Override]
    public function getHeaders(): array
    {
        return $this->headers;
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
        $clone->headers['name'] = $value;

        return $clone;
    }

    #[Override]
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    #[Override]
    public function withStatus(int $code, string $reasonPhrase = ''): self
    {
        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    #[Override]
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}
