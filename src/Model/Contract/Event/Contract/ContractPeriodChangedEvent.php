<?php

declare(strict_types=1);

namespace App\Model\Contract\Event\Contract;

use Override;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\AbstractDomainEvent;
use App\Model\Contract\Entity\Contract\Period;
use DateTimeImmutable;

class ContractPeriodChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly ContractId $contractId,
        private readonly ?Period $oldPeriod,
        private readonly Period $newPeriod
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'contractId'         => $this->contractId->getValue(),
            'oldPeriod'          => $this->oldPeriod?->toArray(),
            'newPeriod'          => $this->newPeriod->toArray(),
        ];
    }
}
