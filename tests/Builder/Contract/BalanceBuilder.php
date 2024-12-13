<?php

declare(strict_types=1);

namespace App\Tests\Builder\Contract;

use App\Model\Contract\Entity\Balance\Balance;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;

class BalanceBuilder
{
    private ContractId $contractId;
    private InsuredPersonId $insuredPersonId;
    private ServiceId $serviceId;
    private LimitType $limitType;
    private float $value = 10;

    public function __construct()
    {
        $this->contractId = new ContractId('id');
        $this->insuredPersonId = new InsuredPersonId('id');
        $this->serviceId = new ServiceId('id');
        $this->limitType = LimitType::quantity();
    }

    public function withContractId(ContractId $contractId): self
    {
        $clone = clone $this;
        $clone->contractId = $contractId;

        return $clone;
    }

    public function withInsuredPersonId(InsuredPersonId $insuredPersonId): self
    {
        $clone = clone $this;
        $clone->insuredPersonId = $insuredPersonId;

        return $clone;
    }

    public function withServiceId(ServiceId $id): self
    {
        $clone = clone $this;
        $clone->serviceId = $id;

        return $clone;
    }

    public function withLimitType(LimitType $limitType): self
    {
        $clone = clone $this;
        $clone->limitType = $limitType;

        return $clone;
    }

    public function withValue(float $value): self
    {
        $clone = clone $this;
        $clone->value = $value;

        return $clone;
    }

    public function build(): Balance
    {
        return new Balance(
            $this->contractId,
            $this->insuredPersonId,
            $this->serviceId,
            $this->limitType,
            $this->value
        );
    }
}
