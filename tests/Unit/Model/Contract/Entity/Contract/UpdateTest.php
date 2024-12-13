<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Contract\Entity\Contract;

use App\Model\Contract\Entity\Contract\Period;
use App\Tests\Builder\Contract\ContractBuilder;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testSuccess(): void
    {
        $contract = (new ContractBuilder())->build();

        $contract->changeNumber($number = 'new number');
        $contract->changeMaxAmount($maxAmount = 100000);

        $period = new Period($now = new DateTimeImmutable(), $now->modify('+1 day'));
        $contract->changePeriod($period);

        self::assertEquals($number, $contract->getNumber());
        self::assertEquals($maxAmount, $contract->getMaxAmount());
        self::assertTrue($contract->getPeriod()->isEqual($period));
        self::assertEquals($period->getStartDate(), $contract->getPeriod()->getStartDate());
    }

    public function testExpired(): void
    {
        $now = new DateTimeImmutable();

        $contract = (new ContractBuilder())
            ->withPeriod(new Period($now, $now->modify('+1 day')))
            ->build();

        self::assertFalse($contract->isExpiredTo($now));
        self::assertTrue($contract->isExpiredTo($now->modify('+2 day')));
    }

    public function testNumberFail(): void
    {
        $contract = (new ContractBuilder())->build();

        self::expectException(InvalidArgumentException::class);
        $contract->changeNumber('');
    }

    public function testMaxAmountFail(): void
    {
        $contract = (new ContractBuilder())->build();

        self::expectException(InvalidArgumentException::class);
        $contract->changeMaxAmount(-1);
    }

    public function testPeriodFail(): void
    {
        $contract = (new ContractBuilder())->build();

        self::expectException(InvalidArgumentException::class);

        $now = new DateTimeImmutable();
        $period = new Period($now->modify('+1 day'), $now);

        $contract->changePeriod($period);
    }
}
