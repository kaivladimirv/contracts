<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\ContractService;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

readonly class ContractServiceId
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Не указан id услуги');
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
}
