<?php

declare(strict_types=1);

namespace App\Framework\Validator\Rules;

use Override;

class EmailDomainRule implements RuleInterface
{
    #[Override]
    public function validate($value): bool
    {
        if (!$domain = $this->extractDomainFrom($value)) {
            return false;
        }

        return $this->isDomainValid($domain);
    }

    private function extractDomainFrom(string $value): string
    {
        $parts = explode('@', $value);

        return (!empty($parts[1]) ? $parts[1] : '');
    }

    private function isDomainValid(string $domain): bool
    {
        return checkdnsrr($domain, 'MX');
    }

    #[Override]
    public function getErrorMessage(): string
    {
        return 'Указан несуществующий домен электронной почты';
    }
}
