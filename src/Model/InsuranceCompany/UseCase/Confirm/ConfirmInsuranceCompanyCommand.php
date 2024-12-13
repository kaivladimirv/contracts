<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\UseCase\Confirm;

class ConfirmInsuranceCompanyCommand
{
    public function __construct(public string $emailConfirmToken)
    {
    }
}
