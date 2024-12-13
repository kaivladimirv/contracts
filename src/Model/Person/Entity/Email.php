<?php

declare(strict_types=1);

namespace App\Model\Person\Entity;

use InvalidArgumentException;

readonly class Email
{
    private string $value;

    public function __construct(string $value)
    {
        $this->assertIsNotEmpty($value);
        $this->assertIsValid($value);

        $this->value = mb_strtolower($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(self $otherEmail): bool
    {
        return $this->value === $otherEmail->getValue();
    }

    private function assertIsNotEmpty(string $value): void
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Не указан email');
        }
    }

    private function assertIsValid(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Некорректно указан email');
        }
    }
}
