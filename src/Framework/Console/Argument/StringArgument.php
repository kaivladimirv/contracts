<?php

declare(strict_types=1);

namespace App\Framework\Console\Argument;

use Override;

readonly class StringArgument implements ArgumentInterface
{
    public function __construct($argumentNameOrNumber, private string $value)
    {
    }

    #[Override]
    public function getValue(): string
    {
        return $this->value;
    }
}
