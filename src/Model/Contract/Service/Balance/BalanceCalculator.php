<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\Balance;

use App\Model\Contract\Entity\Limit\Limit;
use App\ReadModel\ProvidedService\Dto\ExpenseDto;

class BalanceCalculator
{
    public function calc(Limit $limit, ExpenseDto $expense): float
    {
        if ($limit->getType()->isItQuantityLimiter()) {
            return $limit->getValue() - $expense->getQuantity();
        } else {
            return $limit->getValue() - $expense->getAmount();
        }
    }
}
