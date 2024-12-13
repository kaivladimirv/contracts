<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ProvidedService\Registration;

use Override;
use App\Framework\Form\AbstractForm;

class RegistrationProvidedServiceForm extends AbstractForm
{
    #[Override]
    protected function getRules(): array
    {
        return [
            'serviceId'     => [
                'required',
            ],
            'dateOfService' => [
                'required',
                'date',
            ],
            'quantity'      => [
                'required',
                'numeric',
            ],
            'price'         => [
                'required',
                'numeric',
            ],
            'amount'        => [
                'required',
                'numeric',
            ],
        ];
    }
}
