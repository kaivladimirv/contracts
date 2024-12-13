<?php

declare(strict_types=1);

namespace App\Model\Contract\Event\Contract;

use Override;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\AbstractDomainEvent;
use DateTimeImmutable;

class ContractNumberChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly ContractId $contractId,
        private readonly string $oldNumber,
        private readonly string $newNumber
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getInsuranceCompanyId(): InsuranceCompanyId
    {
        return $this->insuranceCompanyId;
    }

    public function getOldNumber(): string
    {
        return $this->oldNumber;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'contractId'         => $this->contractId->getValue(),
            'oldNumber'          => $this->oldNumber,
            'newNumber'          => $this->newNumber,
        ];
    }
}
