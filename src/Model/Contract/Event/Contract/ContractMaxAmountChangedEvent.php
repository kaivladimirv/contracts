<?php

declare(strict_types=1);

namespace App\Model\Contract\Event\Contract;

use Override;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\AbstractDomainEvent;
use DateTimeImmutable;

class ContractMaxAmountChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly ContractId $contractId,
        private readonly float $oldMaxAmount,
        private readonly float $newMaxAmount
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'contractId'         => $this->contractId->getValue(),
            'oldMaxAmount'       => $this->oldMaxAmount,
            'newMaxAmount'       => $this->newMaxAmount,
        ];
    }
}
