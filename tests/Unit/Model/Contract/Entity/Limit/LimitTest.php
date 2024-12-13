<?php

declare(strict_types=1);

namespace Unit\Model\Contract\Entity\Limit;

use App\Model\Contract\Entity\Limit\Limit;
use App\Model\Contract\Entity\Limit\LimitType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class LimitTest extends TestCase
{
    public function testSuccess(): void
    {
        $limit = Limit::quantity($value = 10);

        self::assertEquals($value, $limit->getValue());
        self::assertTrue($limit->getType()->isItQuantityLimiter());

        $limit = Limit::sum($value);

        self::assertTrue($limit->getType()->isItAmountLimiter());
    }

    public function testLimitValueIsGreaterThanZero(): void
    {
        self::expectException(InvalidArgumentException::class);

        Limit::quantity(0);
    }

    public function testLimitTypeDoesNotExist(): void
    {
        self::expectException(InvalidArgumentException::class);

        new Limit(new LimitType(-1), 10);
    }
}
