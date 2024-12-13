<?php

declare(strict_types=1);

namespace App\Model\Contract\Event\Contract;

use Override;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\AbstractDomainEvent;
use App\Model\Contract\Entity\Contract\Period;
use DateTimeImmutable;

class ContractCreatedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly ContractId $contractId,
        private readonly string $number,
        private readonly ?Period $period,
        private readonly float $maxAmount
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'contractId'         => $this->contractId->getValue(),
            'number'             => $this->number,
            'period'             => $this->period->toArray(),
            'maxAmount'          => $this->maxAmount,
        ];
    }
}
