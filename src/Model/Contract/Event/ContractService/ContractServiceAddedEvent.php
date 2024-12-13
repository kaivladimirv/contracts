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

class ContractServiceAddedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly ContractServiceId $id,
        private readonly ContractId $contractId,
        private readonly ServiceId $serviceId,
        private readonly Limit $limit
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
            'limit'             => [
                'type'  => $this->limit->getType()->getValue(),
                'value' => $this->limit->getValue(),
            ],
        ];
    }
}
