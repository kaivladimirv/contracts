<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\InsuredPerson;

use App\Model\Contract\Entity\Contract\ContractId;

interface InsuredPersonFetcherInterface
{
    public function getAll(ContractId $contractId, int $limit, int $skip, Filter $filter): array;

    public function getAllIds(ContractId $contractId): array;
}
