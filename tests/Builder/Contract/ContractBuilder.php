<?php

declare(strict_types=1);

namespace App\Tests\Builder\Contract;

use App\Model\Contract\Entity\Contract\Contract;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\Contract\Entity\Contract\Period;

class ContractBuilder
{
    private ContractId $id;
    private string $number = 'number';
    private InsuranceCompanyId $insuranceCompanyId;
    private ?Period $period = null;
    private float $maxAmount = 0;

    public function __construct()
    {
        $this->id = ContractId::next();
        $this->insuranceCompanyId = new InsuranceCompanyId('id');
    }

    public function withId(ContractId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    public function withNumber(string $number): self
    {
        $clone = clone $this;
        $clone->number = $number;

        return $clone;
    }

    public function withInsuranceCompanyId(InsuranceCompanyId $insuranceCompanyId): self
    {
        $clone = clone $this;
        $clone->insuranceCompanyId = $insuranceCompanyId;

        return $clone;
    }

    public function withPeriod(Period $period): self
    {
        $clone = clone $this;
        $clone->period = $period;

        return $clone;
    }

    public function build(): Contract
    {
        return Contract::create(
            $this->id,
            $this->number,
            $this->insuranceCompanyId,
            $this->period,
            $this->maxAmount
        );
    }
}
