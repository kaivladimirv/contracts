<?php

declare(strict_types=1);

namespace App\Framework\Validator\Rules;

use Override;

class IntegerRule implements RuleInterface
{
    #[Override]
    public function validate($value): bool
    {
        return (filter_var($value, FILTER_VALIDATE_INT) !== false);
    }

    #[Override]
    public function getErrorMessage(): string
    {
        return '%s должен содержать целое число';
    }
}
