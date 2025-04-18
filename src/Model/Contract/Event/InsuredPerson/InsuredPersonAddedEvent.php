<?php

declare(strict_types=1);

namespace App\Model\Contract\Event\InsuredPerson;

use Override;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\AbstractDomainEvent;
use App\Model\Contract\Entity\InsuredPerson\PersonId;
use DateTimeImmutable;

class InsuredPersonAddedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly ContractId $contractId,
        private readonly InsuredPersonId $insuredPersonId,
        private readonly PersonId $personId,
        private readonly string $policyNumber,
        private readonly bool $isAllowedToExceedLimit
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getContractId(): ContractId
    {
        return $this->contractId;
    }

    public function getInsuredPersonId(): InsuredPersonId
    {
        return $this->insuredPersonId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'contractId'             => $this->contractId->getValue(),
            'insuredPersonId'        => $this->insuredPersonId->getValue(),
            'personId'               => $this->personId->getValue(),
            'policyNumber'           => $this->policyNumber,
            'isAllowedToExceedLimit' => (int) $this->isAllowedToExceedLimit,
        ];
    }
}
