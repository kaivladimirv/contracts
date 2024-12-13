<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\Balance;

use App\Model\AggregateRootInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Model\EventTrait;
use DomainException;

class Balance implements AggregateRootInterface
{
    use EventTrait;

    public function __construct(private readonly ContractId $contractId, private readonly InsuredPersonId $insuredPersonId, private readonly ServiceId $serviceId, private readonly LimitType $limitType, private float $balance)
    {
    }

    public function getContractId(): ContractId
    {
        return $this->contractId;
    }

    public function getInsuredPersonId(): InsuredPersonId
    {
        return $this->insuredPersonId;
    }

    public function getServiceId(): ServiceId
    {
        return $this->serviceId;
    }

    public function getLimitType(): LimitType
    {
        return $this->limitType;
    }

    public function getValue(): float
    {
        return $this->balance;
    }

    public function add(float $value): void
    {
        $this->assertValueIsGreaterThanZero($value);

        $this->balance += $value;
    }

    public function subtract(float $value): void
    {
        $this->assertValueIsGreaterThanZero($value);

        $this->balance -= $value;
    }

    private function assertValueIsGreaterThanZero(float $value): void
    {
        if ($value <= 0) {
            throw new DomainException('Значение должно быть больше нуля');
        }
    }

    public function toArray(): array
    {
        return [
            'contract_id' => $this->contractId->getValue(),
            'insured_person_id' => $this->insuredPersonId->getValue(),
            'service_id' => $this->serviceId->getValue(),
            'limit_type' => $this->limitType->getValue(),
            'balance' => $this->balance,
        ];
    }
}
