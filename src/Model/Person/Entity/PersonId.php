<?php

declare(strict_types=1);

namespace App\Model\Person\Entity;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

readonly class PersonId
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Не указан id персоны');
        }

        $this->value = $value;
    }

    public static function next(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(self $otherId): bool
    {
        return $this->value === $otherId->getValue();
    }
}