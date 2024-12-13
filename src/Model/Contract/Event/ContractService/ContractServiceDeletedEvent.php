<?php

declare(strict_types=1);

namespace App\Model\Contract\Event\ContractService;

use Override;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\AbstractDomainEvent;
use App\Model\Contract\Entity\ContractService\ContractServiceId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use DateTimeImmutable;

class ContractServiceDeletedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly ContractServiceId $contractServiceId,
        private readonly ContractId $contractId,
        private readonly ServiceId $serviceId
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'contractServiceId' => $this->contractServiceId->getValue(),
            'contractId'        => $this->contractId->getValue(),
            'serviceId'         => $this->serviceId->getValue(),
        ];
    }
}
