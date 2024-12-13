<?php

declare(strict_types=1);

namespace App\Framework\Console\Argument;

class ArgumentTypes
{
    public const int STRING  = 0;
    public const int INTEGER = 1;
    public const int DATE    = 2;
    public const int ARRAY   = 3;

    public static function get(): array
    {
        return [
            self::STRING,
            self::INTEGER,
            self::DATE,
            self::ARRAY,
        ];
    }
}
