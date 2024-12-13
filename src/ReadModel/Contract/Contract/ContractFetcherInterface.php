<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Contract;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\ReadModel\Contract\Contract\Filter\Filter;

interface ContractFetcherInterface
{
    public function getAll(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip, Filter $filter): array;

    public function isExist(InsuranceCompanyId $insuranceCompanyId, ContractId $contractId): bool;
}
