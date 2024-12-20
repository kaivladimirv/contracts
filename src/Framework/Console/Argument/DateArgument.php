<?php

declare(strict_types=1);

namespace App\Framework\Console\Argument;

use Override;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;

readonly class DateArgument implements ArgumentInterface
{
    private DateTimeImmutable $value;

    /**
     * @throws Exception
     */
    public function __construct($argumentNameOrNumber, string $argumentValue)
    {
        if (!$this->isDate($argumentValue)) {
            throw new InvalidArgumentException("Аргумент $argumentNameOrNumber должен содержать дату в формате дд.мм.гггг");
        }

        $this->value = new DateTimeImmutable($argumentValue);
    }

    private function isDate(string $value): bool
    {
        try {
            $date = new DateTimeImmutable($value);
        } catch (Exception) {
            return false;
        }

        return checkdate(intval($date->format('m')), intval($date->format('d')), intval($date->format('Y')));
    }

    #[Override]
    public function getValue(): DateTimeImmutable
    {
        return $this->value;
    }
}
