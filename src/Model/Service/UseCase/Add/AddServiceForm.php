<?php

declare(strict_types=1);

namespace App\Model\Service\UseCase\Add;

use Override;
use App\Framework\Form\AbstractForm;

class AddServiceForm extends AbstractForm
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
