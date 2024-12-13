<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\ContractService;

use App\Model\Contract\Entity\Contract\ContractId;

interface ContractServiceFetcherInterface
{
    public function getAll(ContractId $contractId, int $limit, int $skip, Filter $filter): array;
}
