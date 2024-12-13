<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\ContractService;

use InvalidArgumentException;

readonly class ServiceId
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Не указан id услуги');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
