<?php

declare(strict_types=1);

namespace App\Model\Contract\Event\ContractService;

use Override;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\AbstractDomainEvent;
use App\Model\Contract\Entity\ContractService\ContractServiceId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\Limit\Limit;
use DateTimeImmutable;

class ContractServiceLimitChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly ContractServiceId $id,
        private readonly ContractId $contractId,
        private readonly ServiceId $serviceId,
        private readonly Limit $oldLimit,
        private readonly Limit $newLimit
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getContractId(): ContractId
    {
        return $this->contractId;
    }

    public function getServiceId(): ServiceId
    {
        return $this->serviceId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'contractServiceId' => $this->id->getValue(),
            'contractId'        => $this->contractId->getValue(),
            'serviceId'         => $this->serviceId->getValue(),
            'oldLimit'          => [
                'type'  => $this->oldLimit->getType()->getValue(),
                'value' => $this->oldLimit->getValue(),
            ],
            'newLimit'          => [
                'type'  => $this->newLimit->getType()->getValue(),
                'value' => $this->newLimit->getValue(),
            ],
        ];
    }
}
