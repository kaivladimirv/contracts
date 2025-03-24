<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Service\Entity;

use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\Service;
use App\Model\Service\Entity\ServiceId;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    public function testSuccess(): void
    {
        $service = Service::create(
            ServiceId::next(),
            'service #1',
            new InsuranceCompanyId('id')
        );

        $service->delete();

        self::assertTrue($service->isDeleted());
    }
}
