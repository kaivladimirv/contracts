<?php

declare(strict_types=1);

namespace App\Tests\Builder\Contract;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Model\Contract\Entity\ProvidedService\Id;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Entity\ProvidedService\Service;
use DateTimeImmutable;

class ProvidedServiceBuilder
{
    private Id $id;
    private ContractId $contractId;
    private InsuredPersonId $insuredPersonId;
    private DateTimeImmutable $dateOfService;
    private LimitType $limitType;
    private ServiceId $serviceId;
    private string $serviceName = 'name';
    private float $quantity = 10;
    private float $price = 1000;

    public function __construct()
    {
        $this->id = new Id('id');
        $this->contractId = new ContractId('id');
        $this->insuredPersonId = new InsuredPersonId('id');
        $this->dateOfService = new DateTimeImmutable();
        $this->serviceId = new ServiceId('id');
        $this->limitType = LimitType::quantity();
    }

    public function withId(Id $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    public function withContractId(ContractId $contractId): self
    {
        $clone = clone $this;
        $clone->contractId = $contractId;

        return $clone;
    }

    public function withInsuredPersonId(InsuredPersonId $insuredPersonId): self
    {
        $clone = clone $this;
        $clone->insuredPersonId = $insuredPersonId;

        return $clone;
    }

    public function withDateOfService(DateTimeImmutable $date): self
    {
        $clone = clone $this;
        $clone->dateOfService = $date;

        return $clone;
    }

    public function withServiceId(ServiceId $id): self
    {
        $clone = clone $this;
        $clone->serviceId = $id;

        return $clone;
    }

    public function withServiceName(string $name): self
    {
        $clone = clone $this;
        $clone->serviceName = $name;

        return $clone;
    }

    public function withQuantity(float $quantity): self
    {
        $clone = clone $this;
        $clone->quantity = $quantity;

        return $clone;
    }

    public function withPrice(float $price): self
    {
        $clone = clone $this;
        $clone->price = $price;

        return $clone;
    }

    public function withLimitType(LimitType $limitType): self
    {
        $clone = clone $this;
        $clone->limitType = $limitType;

        return $clone;
    }

    public function build(): ProvidedService
    {
        return new ProvidedService(
            $this->id,
            $this->contractId,
            $this->insuredPersonId,
            $this->dateOfService,
            new Service(
                $this->serviceId,
                $this->serviceName,
                $this->quantity,
                $this->price,
                $this->quantity * $this->price
            ),
            $this->limitType
        );
    }
}
