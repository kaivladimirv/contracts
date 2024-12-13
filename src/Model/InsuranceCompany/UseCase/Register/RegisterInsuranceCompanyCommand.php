<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\UseCase\Register;

class RegisterInsuranceCompanyCommand
{
    public string $id;
    public string $name;
    public string $email;
    public string $password;
}
