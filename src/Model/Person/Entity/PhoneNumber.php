<?php

declare(strict_types=1);

namespace App\Model\Person\Entity;

use InvalidArgumentException;

readonly class PhoneNumber
{
    private string $value;

    public function __construct(string $value)
    {
        $this->assertIsNumeric($value);
        $this->assertLengthIsCorrect($value);

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(self $otherPhoneNumber): bool
    {
        return $this->value === $otherPhoneNumber->getValue();
    }

    private function assertIsNumeric(string $value): void
    {
        if (!is_numeric($value)) {
            throw new InvalidArgumentException('Номер телефона должен содержать только цифры');
        }
    }

    private function assertLengthIsCorrect(string $value): void
    {
        if (strlen($value) !== 11) {
            throw new InvalidArgumentException('Номер телефона должен содержать 11 цифр');
        }
    }
}
