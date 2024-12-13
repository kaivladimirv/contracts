<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Balance\Dto;

use ArrayIterator;

/**
 * @extends ArrayIterator<int, BalanceDto>
 */
class BalanceDtoCollection extends ArrayIterator
{
    public function toArray(): array
    {
        $array = [];

        /** @var BalanceDto $item */
        foreach ($this as $item) {
            $array[] = $item->toArray();
        }

        return $array;
    }

    public function only(array $keys): array
    {
        $array = [];

        /** @var BalanceDto $item */
        foreach ($this as $item) {
            $array[] = $item->only($keys);
        }

        return $array;
    }
}
