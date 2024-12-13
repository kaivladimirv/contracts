<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ContractService\Delete;

class DeleteContractServiceCommand
{
    public string $contractId;
    public string $serviceId;
}
