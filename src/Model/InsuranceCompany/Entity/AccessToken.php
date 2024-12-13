<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Entity;

use DateTimeImmutable;

readonly class AccessToken
{
    public function __construct(private string $token, private DateTimeImmutable $expires)
    {
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }
}
