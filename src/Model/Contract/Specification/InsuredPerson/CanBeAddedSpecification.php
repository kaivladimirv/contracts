<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\InsuredPerson;

use Override;
use App\Model\AbstractAndSpecification;

class CanBeAddedSpecification extends AbstractAndSpecification
{
    #[Override]
    protected function getSpecificationClassNames(): array
    {
        return [
            PersonIsUniqueSpecification::class,
            ContractIsExistSpecification::class,
            PersonIsExistSpecification::class,
            PolicyNumberIsUniqueSpecification::class,
        ];
    }
}
