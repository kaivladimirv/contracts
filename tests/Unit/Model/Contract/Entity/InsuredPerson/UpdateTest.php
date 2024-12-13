<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Contract\Entity\InsuredPerson;

use App\Tests\Builder\Contract\InsuredPersonBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testSuccess(): void
    {
        $insuredPerson = (new InsuredPersonBuilder())->build();

        $insuredPerson->changePolicyNumber($policyNumber = 'new_policy_number');
        $insuredPerson->allowToExceedLimit();

        self::assertEquals($policyNumber, $insuredPerson->getPolicyNumber());
        self::assertTrue($insuredPerson->isAllowedToExceedLimit());

        $insuredPerson->disallowToExceedLimit();
        self::assertFalse($insuredPerson->isAllowedToExceedLimit());
    }

    public function testPolicyNumberIsNotEmpty(): void
    {
        $insuredPerson = (new InsuredPersonBuilder())->build();

        self::expectException(InvalidArgumentException::class);
        $insuredPerson->changePolicyNumber('');
    }
}
