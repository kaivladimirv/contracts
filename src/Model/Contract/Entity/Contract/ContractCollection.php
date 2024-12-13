<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\Contract;

use ArrayIterator;

/**
 * @extends ArrayIterator<int, Contract>
 */
class ContractCollection extends ArrayIterator
{
    public function toArray(): array
    {
        $array = [];

        foreach ($this as $item) {
            $array[] = $item->toArray();
        }

        return $array;
    }
}
