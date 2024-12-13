<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Service;

use App\Model\InsuranceCompany\Entity\AccessToken;
use DateInterval;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

readonly class AccessTokenGenerator
{
    public function __construct(private DateInterval $interval)
    {
    }

    public function generate(): AccessToken
    {
        return new AccessToken(
            Uuid::uuid4()->toString(),
            (new DateTimeImmutable())->add($this->interval)
        );
    }
}
