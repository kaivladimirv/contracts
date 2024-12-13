<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ContractService\Update;

class UpdateContractServiceCommand
{
    public string $contractId;
    public string $serviceId;
    public float $limitValue;
}
