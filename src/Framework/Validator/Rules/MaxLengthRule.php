<?php

declare(strict_types=1);

namespace App\Framework\Validator\Rules;

use Override;

readonly class MaxLengthRule implements RuleInterface
{
    public function __construct(private mixed $maxLength)
    {
    }

    #[Override]
    public function validate($value): bool
    {
        return strlen((string) $value) <= (int) $this->maxLength;
    }

    #[Override]
    public function getErrorMessage(): string
    {
        return '%s должен быть длинное не более ' . $this->maxLength . ' символов';
    }
}
