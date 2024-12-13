<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Contract\Create;

use DateTimeImmutable;

class CreateContractCommand
{
    public string $id;
    public string $number;
    public string $insuranceCompanyId;
    public DateTimeImmutable $startDate;
    public DateTimeImmutable $endDate;
    public float $maxAmount;
}
