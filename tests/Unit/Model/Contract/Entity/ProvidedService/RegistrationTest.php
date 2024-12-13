<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Contract\Entity\ProvidedService;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Model\Contract\Entity\ProvidedService\Id;
use App\Tests\Builder\Contract\ProvidedServiceBuilder;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

class RegistrationTest extends TestCase
{
    public function testSuccess(): void
    {
        $providedService = (new ProvidedServiceBuilder())
            ->withId($id = new Id('provided_service_id'))
            ->withContractId($contractId = new ContractId('contract_id'))
            ->withInsuredPersonId($insuredPersonId = new InsuredPersonId('insured_person_id'))
            ->withDateOfService($dateOfService = new DateTimeImmutable())
            ->withServiceId($serviceId = new ServiceId('service_id'))
            ->withServiceName($serviceName = 'test')
            ->withQuantity($quantity = 2)
            ->withPrice($price = 1000)
            ->withLimitType($limitType = LimitType::quantity())
            ->build();

        self::assertEquals($id, $providedService->getId());
        self::assertEquals($contractId, $providedService->getContractId());
        self::assertEquals($insuredPersonId, $providedService->getInsuredPersonId());
        self::assertEquals($dateOfService, $providedService->getDateOfService());
        self::assertEquals($serviceId, $providedService->getService()->getId());
        self::assertEquals($serviceName, $providedService->getService()->getName());
        self::assertEquals($quantity, $providedService->getValue());
        self::assertEquals($price, $providedService->getService()->getPrice());
        self::assertTrue($limitType->isEqual($providedService->getLimitType()));
    }

    public function testLimitSumSuccess(): void
    {
        $providedService = (new ProvidedServiceBuilder())
            ->withQuantity($quantity = 2)
            ->withPrice($price = 1000)
            ->withLimitType($limitType = LimitType::sum())
            ->build();

        $sum = $quantity * $price;

        self::assertTrue($limitType->isEqual($providedService->getLimitType()));
        self::assertEquals($sum, $providedService->getValue());
    }
}
