<?php

declare(strict_types=1);

namespace App\Framework\Validator\Rules;

class Rules
{
    public static function get(): array
    {
        return [
            'required'     => RequiredRule::class,
            'nullable'     => NullableRule::class,
            'date'         => DateRule::class,
            'numeric'      => NumericRule::class,
            'integer'      => IntegerRule::class,
            'email'        => EmailRule::class,
            'email-domain' => EmailDomainRule::class,
            'password'     => PasswordRule::class,
            'max-length'   => MaxLengthRule::class
        ];
    }
}
