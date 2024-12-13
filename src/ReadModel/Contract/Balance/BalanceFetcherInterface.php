<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Balance;

use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\ReadModel\Contract\Balance\Dto\BalanceDtoCollection;

interface BalanceFetcherInterface
{
    public function getAllByInsuredPersonId(InsuredPersonId $insuredPersonId): BalanceDtoCollection;
}
