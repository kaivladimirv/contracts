<?php

declare(strict_types=1);

namespace App\Framework\Validator\Rules;

use Override;

class NumericRule implements RuleInterface
{
    #[Override]
    public function validate($value): bool
    {
        return is_numeric($value);
    }

    #[Override]
    public function getErrorMessage(): string
    {
        return '%s должен быть числовым значением';
    }
}
