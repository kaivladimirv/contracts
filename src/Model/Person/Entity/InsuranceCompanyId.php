<?php

declare(strict_types=1);

namespace App\Model\Person\Entity;

use InvalidArgumentException;

readonly class InsuranceCompanyId
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Не указан id страховой компании');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
