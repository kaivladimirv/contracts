<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Contract\Entity\Balance;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Tests\Builder\Contract\BalanceBuilder;
use DomainException;
use PHPUnit\Framework\TestCase;

class BalanceTest extends TestCase
{
    public function testSuccess(): void
    {
        $balance = (new BalanceBuilder())
            ->withContractId($contractId = ContractId::next())
            ->withInsuredPersonId($insuredPersonId = InsuredPersonId::next())
            ->withServiceId($serviceId = new ServiceId('1223'))
            ->withLimitType($limitType = LimitType::quantity())
            ->withValue($value = 20)
            ->build();

        self::assertEquals($contractId, $balance->getContractId());
        self::assertEquals($insuredPersonId, $balance->getInsuredPersonId());
        self::assertEquals($serviceId, $balance->getServiceId());
        self::assertTrue($limitType->isEqual($balance->getLimitType()));
        self::assertEquals($value, $balance->getValue());

        $addedValue = 20;
        $expectedValue = $balance->getValue() + (float) $addedValue;
        $balance->add($addedValue);
        self::assertEquals($expectedValue, $balance->getValue());

        $subtractedValue = 20;
        $expectedValue = $balance->getValue() - (float) $subtractedValue;
        $balance->subtract($subtractedValue);
        self::assertEquals($expectedValue, $balance->getValue());
    }

    public function testAddFail(): void
    {
        $balance = (new BalanceBuilder())->build();

        self::expectException(DomainException::class);

        $balance->add(-1);
    }

    public function testSubtractFail(): void
    {
        $balance = (new BalanceBuilder())->build();

        self::expectException(DomainException::class);

        $balance->subtract(-1);
    }
}
