<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\UseCase\Update;

use Override;
use App\Framework\Form\AbstractForm;

class UpdateInsuranceCompanyForm extends AbstractForm
{
    #[Override]
    protected function getRules(): array
    {
        return [
            'name' => [
                'required',
            ],
        ];
    }
}
