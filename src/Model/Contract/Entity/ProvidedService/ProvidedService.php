<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\ProvidedService;

use App\Model\AggregateRootInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Model\Contract\Event\ProvidedService\ProvidedServiceCanceledEvent;
use App\Model\Contract\Event\ProvidedService\ProvidedServiceRegisteredEvent;
use App\Model\EventTrait;
use DateTimeImmutable;
use DomainException;

class ProvidedService implements AggregateRootInterface
{
    use EventTrait;

    private bool $isDeleted = false;
    private ?DateTimeImmutable $deletionDate = null;

    public function __construct(
        private readonly Id $id,
        private readonly ContractId $contractId,
        private readonly InsuredPersonId $insuredPersonId,
        private readonly DateTimeImmutable $dateOfService,
        private readonly Service $service,
        private readonly LimitType $limitType
    ) {
        $this->registerEvent(
            new ProvidedServiceRegisteredEvent(
                $this->id,
                $this->contractId,
                $this->insuredPersonId,
                $this->dateOfService,
                $this->service,
                $this->limitType
            )
        );
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getContractId(): ContractId
    {
        return $this->contractId;
    }

    public function getInsuredPersonId(): InsuredPersonId
    {
        return $this->insuredPersonId;
    }

    public function getDateOfService(): DateTimeImmutable
    {
        return $this->dateOfService;
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function getLimitType(): LimitType
    {
        return $this->limitType;
    }

    public function isRegistrationCanceled(): bool
    {
        return $this->isDeleted;
    }

    public function getValue(): float
    {
        return $this->limitType->isItAmountLimiter() ? $this->service->getAmount() : $this->service->getQuantity();
    }

    public function cancelRegistration(DateTimeImmutable $date): void
    {
        if ($this->isDeleted) {
            throw new DomainException('Услуга уже отменена');
        }

        $this->isDeleted = true;
        $this->deletionDate = $date;

        $this->registerEvent(
            new ProvidedServiceCanceledEvent(
                $this->id,
                $this->contractId,
                $this->insuredPersonId,
                $this->service,
                $this->limitType,
                $this->deletionDate
            )
        );
    }

    public function getRegistrationCanceledDate(): ?DateTimeImmutable
    {
        return $this->deletionDate;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'contract_id' => $this->contractId->getValue(),
            'insured_person_id' => $this->insuredPersonId->getValue(),
            'date_of_service' => $this->dateOfService->format('c'),
            'service_id' => $this->service->getId()->getValue(),
            'service_name' => $this->service->getName(),
            'limit_type' => $this->limitType->getValue(),
            'quantity' => $this->service->getQuantity(),
            'price' => $this->service->getPrice(),
            'amount' => $this->service->getAmount(),
            'is_deleted' => $this->isDeleted ? 1 : 0,
            'deletion_date' => $this->deletionDate?->format('c'),
        ];
    }
}
