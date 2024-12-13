<?php

declare(strict_types=1);

namespace App\Model\Contract\Event\InsuredPerson;

use Override;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\AbstractDomainEvent;
use DateTimeImmutable;

class PolicyNumberChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly ContractId $contractId,
        private readonly InsuredPersonId $insuredPersonId,
        private readonly string $oldPolicyNumber,
        private readonly string $newPolicyNumber
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getContractId(): ContractId
    {
        return $this->contractId;
    }

    public function getOldPolicyNumber(): string
    {
        return $this->oldPolicyNumber;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'contractId'      => $this->contractId->getValue(),
            'insuredPersonId' => $this->insuredPersonId->getValue(),
            'oldPolicyNumber' => $this->oldPolicyNumber,
            'newPolicyNumber' => $this->newPolicyNumber,
        ];
    }
}
