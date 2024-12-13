<?php

declare(strict_types=1);

namespace App\Model\Person\Specification;

use Override;
use App\Model\AbstractAndSpecification;

class CanBeAddedOrUpdatedSpecification extends AbstractAndSpecification
{
    #[Override]
    protected function getSpecificationClassNames(): array
    {
        return [
            NameIsUniqueSpecification::class,
            EmailIsUniqueSpecification::class,
            PhoneNumberIsUniqueSpecification::class,
        ];
    }
}
