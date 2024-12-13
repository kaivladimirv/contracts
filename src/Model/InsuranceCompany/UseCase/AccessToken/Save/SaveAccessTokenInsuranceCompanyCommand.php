<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\UseCase\AccessToken\Save;

class SaveAccessTokenInsuranceCompanyCommand
{
    public function __construct(public string $insuranceCompanyId, public string $accessToken, public string $expires)
    {
    }
}
