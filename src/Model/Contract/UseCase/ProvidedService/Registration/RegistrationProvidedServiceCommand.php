<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ProvidedService\Registration;

use DateTimeImmutable;

class RegistrationProvidedServiceCommand
{
    public string $id;
    public string $insuredPersonId;
    public string $serviceId;
    public DateTimeImmutable $dateOfService;
    public float $quantity;
    public float $price;
    public float $amount;
}
