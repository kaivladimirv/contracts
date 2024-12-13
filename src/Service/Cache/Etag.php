<?php

declare(strict_types=1);

namespace App\Service\Cache;

use Ramsey\Uuid\Nonstandard\Uuid;

readonly class Etag
{
    public function __construct(private string $value)
    {
    }

    public static function next(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(self $otherEtag): bool
    {
        return $this->value === $otherEtag->getValue();
    }
}
