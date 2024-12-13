<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Balance\Recalc\ByInsured;

class RecalcBalanceByInsuredCommand
{
    public string $contractId;
    public string $insuredPersonId;
}
