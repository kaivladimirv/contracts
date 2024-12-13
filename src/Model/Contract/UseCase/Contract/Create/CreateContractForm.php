<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Contract\Create;

use Override;
use App\Framework\Form\AbstractForm;

class CreateContractForm extends AbstractForm
{
    #[Override]
    protected function getRules(): array
    {
        return [
            'number'    => [
                'required',
            ],
            'startDate' => [
                'required',
                'date',
            ],
            'endDate'   => [
                'required',
                'date',
            ],
            'maxAmount' => [
                'required',
                'numeric',
            ],
        ];
    }
}
