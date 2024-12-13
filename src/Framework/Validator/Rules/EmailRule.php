<?php

declare(strict_types=1);

namespace App\Framework\Validator\Rules;

use Override;

class EmailRule implements RuleInterface
{
    #[Override]
    public function validate($value): bool
    {
        return $this->isEmailValid($value);
    }

    private function isEmailValid(string $email): bool
    {
        $pattern = "/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix";

        return !!preg_match($pattern, $email);
    }

    #[Override]
    public function getErrorMessage(): string
    {
        return '%s содержит некорректный адрес электронной почты';
    }
}
