<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ContractService\Add;

class AddContractServiceCommand
{
    public string $id;
    public string $contractId;
    public string $serviceId;
    public int $limitType;
    public float $limitValue;
}
