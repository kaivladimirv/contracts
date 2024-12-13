<?php

declare(strict_types=1);

namespace App\Model\Service\UseCase\Update;

use Override;
use App\Framework\Form\AbstractForm;

class UpdateServiceForm extends AbstractForm
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
