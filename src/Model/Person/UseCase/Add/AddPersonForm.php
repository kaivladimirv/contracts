<?php

declare(strict_types=1);

namespace App\Model\Person\UseCase\Add;

use Override;
use App\Framework\Form\AbstractForm;

class AddPersonForm extends AbstractForm
{
    #[Override]
    protected function getRules(): array
    {
        return [
            'lastName'     => [
                'required',
            ],
            'firstName'    => [
                'required',
            ],
            'middleName'   => [
                'required',
            ],
            'email'        => [
                'nullable',
                'email',
                'email-domain',
            ],
            'phoneNumber'  => [
                'nullable',
                'numeric',
            ],
            'notifierType' => [
                'nullable',
            ],
        ];
    }
}
