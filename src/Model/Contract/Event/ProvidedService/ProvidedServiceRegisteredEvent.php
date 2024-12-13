<?php

declare(strict_types=1);

namespace App\Model\Contract\Event\ProvidedService;

use Override;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\AbstractDomainEvent;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Model\Contract\Entity\ProvidedService\Id;
use App\Model\Contract\Entity\ProvidedService\Service;
use DateTimeImmutable;

class ProvidedServiceRegisteredEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly Id $providedServiceId,
        private readonly ContractId $contractId,
        private readonly InsuredPersonId $insuredPersonId,
        private readonly DateTimeImmutable $dateOfService,
        private readonly Service $service,
        private readonly LimitType $limitType
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

    public function getService(): Service
    {
        return $this->service;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'providedServiceId' => $this->providedServiceId->getValue(),
            'contractId' => $this->contractId->getValue(),
            'insuredPersonId' => $this->insuredPersonId->getValue(),
            'dateOfService' => $this->dateOfService->format('c'),
            'service' => $this->service->toArray(),
            'limitType' => $this->limitType->getValue(),
        ];
    }
}
