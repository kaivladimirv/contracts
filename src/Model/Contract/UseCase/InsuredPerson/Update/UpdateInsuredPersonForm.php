<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\InsuredPerson\Update;

use Override;
use App\Framework\Form\AbstractForm;

class UpdateInsuredPersonForm extends AbstractForm
{
    #[Override]
    protected function getRules(): array
    {
        return [
            'policyNumber'           => [
                'required',
            ],
            'isAllowedToExceedLimit' => [
                'required',
            ],
        ];
    }
}
