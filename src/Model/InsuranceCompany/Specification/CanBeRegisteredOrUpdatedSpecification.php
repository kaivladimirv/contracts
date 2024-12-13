<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Specification;

use Override;
use App\Model\AbstractAndSpecification;

class CanBeRegisteredOrUpdatedSpecification extends AbstractAndSpecification
{
    #[Override]
    protected function getSpecificationClassNames(): array
    {
        return [
            NameIsUniqueSpecification::class,
            EmailIsUniqueSpecification::class,
        ];
    }
}
