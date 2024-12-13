<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Service;

use Ramsey\Uuid\Uuid;

class EmailConfirmTokenGenerator
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
