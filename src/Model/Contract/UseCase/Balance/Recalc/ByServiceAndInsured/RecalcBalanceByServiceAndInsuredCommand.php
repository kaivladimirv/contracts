<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Balance\Recalc\ByServiceAndInsured;

class RecalcBalanceByServiceAndInsuredCommand
{
    public string $contractId;
    public string $serviceId;
    public string $insuredPersonId;
}
