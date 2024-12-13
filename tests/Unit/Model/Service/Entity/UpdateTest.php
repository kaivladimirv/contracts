<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Service\Entity;

use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\Service;
use App\Model\Service\Entity\ServiceId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testSuccess(): void
    {
        $service = Service::create(
            ServiceId::next(),
            'service #1',
            new InsuranceCompanyId('id')
        );

        $service->changeName($newName = 'service #2');

        self::assertEquals($newName, $service->getName());
    }

    public function testNameNotSpecified(): void
    {
        $service = Service::create(
            ServiceId::next(),
            'service #1',
            new InsuranceCompanyId('id')
        );

        self::expectException(InvalidArgumentException::class);
        $service->changeName('');
    }
}
