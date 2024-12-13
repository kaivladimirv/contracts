<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Contract\Entity\Contract;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Tests\Builder\Contract\ContractBuilder;
use PHPUnit\Framework\TestCase;

class AddTest extends TestCase
{
    public function testSuccess(): void
    {
        $contract = (new ContractBuilder())
            ->withId($id = ContractId::next())
            ->withNumber($number = 'number')
            ->withInsuranceCompanyId($insuranceCompanyId = new InsuranceCompanyId('id'))
            ->build();

        self::assertEquals($id, $contract->getId());
        self::assertEquals($number, $contract->getNumber());
        self::assertEquals($insuranceCompanyId, $contract->getInsuranceCompanyId());
    }
}
