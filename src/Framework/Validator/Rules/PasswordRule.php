<?php

declare(strict_types=1);

namespace App\Framework\Validator\Rules;

use Override;

class PasswordRule implements RuleInterface
{
    private const int MIN_LENGTH = 8;
    private string $errorMassage = '';

    #[Override]
    public function validate($value): bool
    {
        if (!$this->isGreaterThanOrEqualToMinLength($value)) {
            $this->errorMassage = '%s должен содержать не менее ' . self::MIN_LENGTH . ' символов';

            return false;
        }

        if (!$this->containsOneDigit($value)) {
            $this->errorMassage = '%s должен содержать хотя бы одну цифру.';

            return false;
        }

        if (!$this->containsOneUpperCaseLetter($value)) {
            $this->errorMassage = '%s должен содержать хотя бы одну заглавную букву.';

            return false;
        }

        if (!$this->containsOneLowerCaseLetter($value)) {
            $this->errorMassage = '%s должен содержать хотя бы одну строчную букву.';

            return false;
        }

        return true;
    }

    private function isGreaterThanOrEqualToMinLength(string $value): bool
    {
        return (strlen($value) >= self::MIN_LENGTH);
    }

    private function containsOneDigit(string $value): bool
    {
        return preg_match("#[0-9]+#", $value) > 0;
    }

    private function containsOneUpperCaseLetter(string $value): bool
    {
        return preg_match("#[A-Z]+#", $value) > 0;
    }

    private function containsOneLowerCaseLetter(string $value): bool
    {
        return preg_match("#[a-z]+#", $value) > 0;
    }

    #[Override]
    public function getErrorMessage(): string
    {
        return $this->errorMassage;
    }
}
