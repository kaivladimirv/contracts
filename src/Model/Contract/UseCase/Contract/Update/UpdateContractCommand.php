<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Contract\Update;

use DateTimeImmutable;

class UpdateContractCommand
{
    public string $id;
    public string $number;
    public DateTimeImmutable $startDate;
    public DateTimeImmutable $endDate;
    public float $maxAmount;
}
