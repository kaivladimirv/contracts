<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\InsuredPerson;

use InvalidArgumentException;

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

    public function getValue(): string
    {
        return $this->value;
    }
}
