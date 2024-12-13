<?php

declare(strict_types=1);

namespace App\Framework\Console\Argument;

use Exception;
use UnexpectedValueException;

class ArgumentFactory
{
    /**
     * @throws Exception
     */
    public function create(int $argumentType, $argumentNameOrNumber, string $argumentValue): ArgumentInterface
    {
        return match ($argumentType) {
            ArgumentTypes::STRING => new StringArgument($argumentNameOrNumber, $argumentValue),
            ArgumentTypes::INTEGER => new IntegerArgument($argumentNameOrNumber, $argumentValue),
            ArgumentTypes::DATE => new DateArgument($argumentNameOrNumber, $argumentValue),
            ArgumentTypes::ARRAY => new ArrayArgument($argumentNameOrNumber, $argumentValue),
            default => throw new UnexpectedValueException('Неизвестный тип аргумента'),
        };
    }
}
