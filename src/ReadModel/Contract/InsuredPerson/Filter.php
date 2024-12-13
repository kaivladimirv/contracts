<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\InsuredPerson;

class Filter
{
    public ?string $personName = null;
    public ?string $policyNumber = null;
    public ?bool $isAllowedToExceedLimit = null;
}
