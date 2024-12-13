<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\InsuredPerson\Add;

class AddInsuredPersonCommand
{
    public string $id;
    public string $contractId;
    public string $personId;
    public string $policyNumber;
    public bool $isAllowedToExceedLimit;
}
