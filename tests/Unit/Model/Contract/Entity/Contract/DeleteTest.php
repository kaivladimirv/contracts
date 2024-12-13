<?php

declare(strict_types=1);

namespace Unit\Model\Contract\Entity\Contract;

use App\Tests\Builder\Contract\ContractBuilder;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    public function testSuccess(): void
    {
        $contract = (new ContractBuilder())->build();

        self::assertFalse($contract->isDeleted());

        $contract->delete();
        self::assertTrue($contract->isDeleted());
    }
}
