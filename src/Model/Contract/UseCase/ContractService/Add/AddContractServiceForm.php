<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ContractService\Add;

use Override;
use App\Framework\Form\AbstractForm;

class AddContractServiceForm extends AbstractForm
{
    #[Override]
    protected function getRules(): array
    {
        return [
            'serviceId'  => [
                'required',
            ],
            'limitType'  => [
                'required',
                'integer',
            ],
            'limitValue' => [
                'required',
                'numeric',
            ],
        ];
    }
}
