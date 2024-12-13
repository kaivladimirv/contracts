<?php

declare(strict_types=1);

namespace App\Framework\Console\Argument;

use Override;
use InvalidArgumentException;

readonly class IntegerArgument implements ArgumentInterface
{
    private int $value;

    public function __construct($argumentNameOrNumber, string $argumentValue)
    {
        if (filter_var($argumentValue, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException("Аргумент $argumentNameOrNumber должен содержать целочисленное значение");
        }

        $this->value = intval($argumentValue);
    }

    #[Override]
    public function getValue(): int
    {
        return $this->value;
    }
}
