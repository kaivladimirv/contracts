<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Contract\Entity\ProvidedService;

use App\Tests\Builder\Contract\ProvidedServiceBuilder;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\TestCase;

class CancelRegistrationTest extends TestCase
{
    public function testSuccess(): void
    {
        $providedService = (new ProvidedServiceBuilder())->build();

        $canceledDate = new DateTimeImmutable();
        $providedService->cancelRegistration($canceledDate);

        self::assertTrue($providedService->isRegistrationCanceled());
        self::assertEquals($canceledDate, $providedService->getRegistrationCanceledDate());
    }

    public function testAlreadyCanceled(): void
    {
        $providedService = (new ProvidedServiceBuilder())->build();

        $date = new DateTimeImmutable();
        $providedService->cancelRegistration($date);

        self::expectException(DomainException::class);
        $providedService->cancelRegistration($date);
    }
}
