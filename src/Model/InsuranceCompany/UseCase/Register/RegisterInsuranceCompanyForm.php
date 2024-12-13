<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\UseCase\Register;

use Override;
use App\Framework\Form\AbstractForm;

class RegisterInsuranceCompanyForm extends AbstractForm
{
    #[Override]
    protected function getRules(): array
    {
        return [
            'name'     => [
                'required',
            ],
            'email'    => [
                'required',
                'email',
                'email-domain',
            ],
            'password' => [
                'required',
                'password',
            ],
        ];
    }
}
