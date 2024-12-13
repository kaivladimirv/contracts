<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ContractService\Update;

use Override;
use App\Framework\Form\AbstractForm;

class UpdateContractServiceForm extends AbstractForm
{
    #[Override]
    protected function getRules(): array
    {
        return [
            'limitValue' => [
                'required',
                'numeric',
            ],
        ];
    }
}
