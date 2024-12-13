<?php

declare(strict_types=1);

namespace App\Framework\Validator\Rules;

use Override;

class NullableRule implements RuleInterface
{
    #[Override]
    public function validate($value): bool
    {
        return empty($value);
    }

    #[Override]
    public function getErrorMessage(): string
    {
        return '%s не является пустым';
    }
}
