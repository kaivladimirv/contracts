<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\ProvidedService;

use Override;
use App\Model\AbstractAndSpecification;

class CanBeRegisteredSpecification extends AbstractAndSpecification
{
    #[Override]
    protected function getSpecificationClassNames(): array
    {
        return [
            ContractIsValidSpecification::class,
            ServiceIsCoveredSpecification::class,
            DateOfServiceIsIncludedInContractPeriodSpecification::class,
            MaxAmountUnderContractIsNotExceededSpecification::class,
            ServiceDoesNotExceedLimitSpecification::class
        ];
    }
}
