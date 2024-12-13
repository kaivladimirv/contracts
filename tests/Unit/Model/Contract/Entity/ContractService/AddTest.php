<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Contract\Entity\ContractService;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ContractService;
use App\Model\Contract\Entity\ContractService\ContractServiceId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\Limit\Limit;
use PHPUnit\Framework\TestCase;

class AddTest extends TestCase
{
    public function testSuccess(): void
    {
        $service = new ContractService(
            $id = ContractServiceId::next(),
            $contractId = new ContractId('contract_id'),
            $serviceId = new ServiceId('service_id'),
            $limit = Limit::quantity(10)
        );

        self::assertEquals($id, $service->getId());
        self::assertEquals($contractId, $service->getContractId());
        self::assertEquals($serviceId, $service->getServiceId());
        self::assertEquals($limit, $service->getLimit());
    }
}
