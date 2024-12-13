<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\Balance;

use App\Model\Contract\Entity\Balance\Balance;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Model\Contract\Exception\Balance\BalanceNotFoundException;

interface BalanceRepositoryInterface
{
    /**
     * @throws BalanceNotFoundException
     */
    public function getOne(InsuredPersonId $insuredPersonId, ServiceId $serviceId, LimitType $limitType): Balance;

    public function add(Balance $balance): void;

    public function update(Balance $balance): void;

    public function save(Balance $balance): void;
}
