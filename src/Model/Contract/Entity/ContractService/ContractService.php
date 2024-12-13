<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\ContractService;

use App\Model\AggregateRootInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Limit\Limit;
use App\Model\Contract\Event\ContractService\ContractServiceAddedEvent;
use App\Model\Contract\Event\ContractService\ContractServiceDeletedEvent;
use App\Model\Contract\Event\ContractService\ContractServiceLimitChangedEvent;
use App\Model\EventTrait;

class ContractService implements AggregateRootInterface
{
    use EventTrait;

    private bool $isDeleted = false;

    public function __construct(
        private readonly ContractServiceId $id,
        private readonly ContractId $contractId,
        private readonly ServiceId $serviceId,
        private Limit $limit
    ) {
        $this->registerEvent(
            new ContractServiceAddedEvent(
                $this->id,
                $this->contractId,
                $this->serviceId,
                $this->limit
            )
        );
    }

    public function getId(): ContractServiceId
    {
        return $this->id;
    }

    public function getContractId(): ContractId
    {
        return $this->contractId;
    }

    public function getServiceId(): ServiceId
    {
        return $this->serviceId;
    }

    public function getLimit(): Limit
    {
        return $this->limit;
    }

    public function changeLimit(Limit $newLimit): void
    {
        $oldLimit = $this->limit;
        $this->limit = $newLimit;

        if (!$oldLimit->isEqual($newLimit)) {
            $this->registerEvent(
                new ContractServiceLimitChangedEvent(
                    $this->id,
                    $this->contractId,
                    $this->serviceId,
                    $oldLimit,
                    $newLimit
                )
            );
        }
    }

    public function delete(): void
    {
        $isAlreadyDeleted = $this->isDeleted;
        $this->isDeleted = true;

        if (!$isAlreadyDeleted) {
            $this->registerEvent(
                new ContractServiceDeletedEvent(
                    $this->id,
                    $this->contractId,
                    $this->serviceId
                )
            );
        }
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id->getValue(),
            'contract_id' => $this->contractId->getValue(),
            'service_id'  => $this->serviceId->getValue(),
            'limit_type'  => $this->limit->getType()->getValue(),
            'limit_value' => $this->limit->getValue(),
        ];
    }
}
