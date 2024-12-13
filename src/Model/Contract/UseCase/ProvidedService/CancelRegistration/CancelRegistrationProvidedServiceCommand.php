<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ProvidedService\CancelRegistration;

use DateTimeImmutable;

class CancelRegistrationProvidedServiceCommand
{
    public string $id;
    public DateTimeImmutable $date;
}
