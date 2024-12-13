<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\InsuredPerson\Update;

class UpdateInsuredPersonCommand
{
    public string $insuredPersonId;
    public string $policyNumber;
    public bool $isAllowedToExceedLimit;
}
