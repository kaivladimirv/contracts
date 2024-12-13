<?php

declare(strict_types=1);

namespace App\Model\Contract\Event\InsuredPerson;

use Override;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\AbstractDomainEvent;
use DateTimeImmutable;

class ExceedLimitAllowedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly ContractId $contractId,
        private readonly InsuredPersonId $insuredPersonId
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'contractId'      => $this->contractId->getValue(),
            'insuredPersonId' => $this->insuredPersonId->getValue(),
        ];
    }
}
