<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Balance\Recalc\Service;

class RecalcBalanceCommand
{
    public string $contractId;
    public string $serviceId;
}
