<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\Limit;

use InvalidArgumentException;

readonly class Limit
{
    private float $value;

    public function __construct(private LimitType $type, float $value)
    {
        $this->assertValueIsGreaterThanZero($value);
        $this->value = $value;
    }

    private function assertValueIsGreaterThanZero(float $value): void
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('Значение лимита должно быть больше нуля');
        }
    }

    public static function sum(float $value): self
    {
        return new self(LimitType::sum(), $value);
    }

    public static function quantity(float $value): self
    {
        return new self(LimitType::quantity(), $value);
    }

    public function getType(): LimitType
    {
        return $this->type;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function isEqual(self $otherLimit): bool
    {
        return $this->type->isEqual($otherLimit->getType())
            and $this->value === $otherLimit->getValue();
    }
}
