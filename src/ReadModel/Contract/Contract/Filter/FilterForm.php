<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Contract\Filter;

use Override;
use App\Framework\Form\AbstractForm;

class FilterForm extends AbstractForm
{
    protected string $requestMethod = 'GET';

    #[Override]
    protected function getRules(): array
    {
        return [
            'page'   => [
                'required',
                'integer',
            ],
            'number' => [
            ],
        ];
    }
}
