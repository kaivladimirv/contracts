<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\Limit;

use InvalidArgumentException;

class LimitType
{
    public const int SUM = 0;
    public const int QUANTITY = 1;

    private readonly int $value;

    public function __construct(int $type)
    {
        $this->assertTypeIsExists($type);

        $this->value = $type;
    }

    private function assertTypeIsExists(int $type): void
    {
        if (!self::isExists($type)) {
            throw new InvalidArgumentException('Неизвестный тип лимита');
        }
    }

    public static function sum(): self
    {
        return new self(self::SUM);
    }

    public static function quantity(): self
    {
        return new self(self::QUANTITY);
    }

    public static function isExists(int $type): bool
    {
        return array_key_exists($type, static::getAll());
    }

    public static function getAll(): array
    {
        return [
            self::SUM => 'Ограничение по сумме',
            self::QUANTITY => 'Ограничение по количеству',
        ];
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function isItAmountLimiter(): bool
    {
        return $this->value === self::SUM;
    }

    public function isItQuantityLimiter(): bool
    {
        return $this->value === self::QUANTITY;
    }

    public function isEqual(self $otherLimit): bool
    {
        return $this->value === $otherLimit->getValue();
    }
}
