<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Service\Entity;

use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\Service;
use App\Model\Service\Entity\ServiceId;
use PHPUnit\Framework\TestCase;

class AddTest extends TestCase
{
    public function testSuccess(): void
    {
        $service = Service::create(
            $id = ServiceId::next(),
            $name = 'service',
            $insuranceCompanyId = new InsuranceCompanyId('id')
        );

        self::assertEquals($id, $service->getId());
        self::assertEquals($name, $service->getName());
        self::assertEquals($insuranceCompanyId, $service->getInsuranceCompanyId());
    }
}
