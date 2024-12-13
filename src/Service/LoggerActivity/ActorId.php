<?php

declare(strict_types=1);

namespace App\Service\LoggerActivity;

use InvalidArgumentException;

readonly class ActorId
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('ActorId not specified');
        }

        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
