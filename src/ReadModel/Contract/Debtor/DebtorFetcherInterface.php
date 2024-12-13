<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Debtor;

use App\Model\Contract\Entity\Contract\ContractId;
use App\ReadModel\Contract\Debtor\Dto\DebtorDtoCollection;

interface DebtorFetcherInterface
{
    public function get(ContractId $contractId): DebtorDtoCollection;
}
