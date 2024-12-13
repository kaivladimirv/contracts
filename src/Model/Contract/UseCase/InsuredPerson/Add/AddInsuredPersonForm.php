<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\InsuredPerson\Add;

use Override;
use App\Framework\Form\AbstractForm;

class AddInsuredPersonForm extends AbstractForm
{
    #[Override]
    protected function getRules(): array
    {
        return [
            'contractId'             => [
                'required',
                'max-length:36'
            ],
            'personId'               => [
                'required',
                'max-length:36'
            ],
            'policyNumber'           => [
                'required',
            ],
            'isAllowedToExceedLimit' => [
                'required',
            ],
        ];
    }
}
