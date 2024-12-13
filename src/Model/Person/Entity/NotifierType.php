<?php

declare(strict_types=1);

namespace App\Model\Person\Entity;

use InvalidArgumentException;

class NotifierType
{
    public const int EMAIL    = 0;
    public const int TELEGRAM = 1;

    private readonly int $value;

    public function __construct(int $value)
    {
        if (!in_array($value, $this->getAll())) {
            throw new InvalidArgumentException('Указан неизвестный тип уведомителя');
        }

        $this->value = $value;
    }

    public static function email(): self
    {
        return new self(self::EMAIL);
    }

    public static function telegram(): self
    {
        return new self(self::TELEGRAM);
    }

    private function getAll(): array
    {
        return [
            self::EMAIL,
            self::TELEGRAM,
        ];
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function isEqual(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    public function isEmail(): bool
    {
        return $this->value === self::EMAIL;
    }

    public function isTelegram(): bool
    {
        return $this->value === self::TELEGRAM;
    }
}
