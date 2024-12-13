<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\ContractService;

use ArrayIterator;

/**
 * @extends ArrayIterator<int, ContractService>
 */
class ContractServiceCollection extends ArrayIterator
{
    public function toArray(): array
    {
        $array = [];

        /** @var ContractService $item */
        foreach ($this as $item) {
            $array[] = $item->toArray();
        }

        return $array;
    }
}
