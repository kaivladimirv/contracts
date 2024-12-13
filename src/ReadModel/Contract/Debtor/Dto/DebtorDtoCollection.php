<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Debtor\Dto;

use ArrayIterator;

/**
 * @extends ArrayIterator<int, DebtorDto>
 */
class DebtorDtoCollection extends ArrayIterator
{
    public function toArray(): array
    {
        $array = [];

        /** @var DebtorDto $item */
        foreach ($this as $item) {
            $array[] = $item->toArray();
        }

        return $array;
    }
}
