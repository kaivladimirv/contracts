<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\ContractService;

use Override;
use App\Model\AbstractAndSpecification;

class CanBeAddedSpecification extends AbstractAndSpecification
{
    #[Override]
    protected function getSpecificationClassNames(): array
    {
        return [
            IsUniqueSpecification::class,
            ServiceIsExistSpecification::class,
        ];
    }
}
