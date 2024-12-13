<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Contract\Entity\ContractService;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ContractService;
use App\Model\Contract\Entity\ContractService\ContractServiceId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\Limit\Limit;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testSuccess(): void
    {
        $service = new ContractService(
            ContractServiceId::next(),
            new ContractId('contract_id'),
            new ServiceId('service_id'),
            Limit::quantity(10)
        );

        $service->changeLimit($limit = Limit::quantity(20));

        self::assertEquals($limit, $service->getLimit());
        self::assertTrue($limit->getType()->isItQuantityLimiter());
    }
}
