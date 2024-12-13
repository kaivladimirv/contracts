<?php

declare(strict_types=1);

namespace App\Framework\Validator\Rules;

use Override;

class RequiredRule implements RuleInterface
{
    #[Override]
    public function validate($value): bool
    {
        return (!empty($value) or is_numeric($value));
    }

    #[Override]
    public function getErrorMessage(): string
    {
        return '%s обязательно для заполнения';
    }
}
