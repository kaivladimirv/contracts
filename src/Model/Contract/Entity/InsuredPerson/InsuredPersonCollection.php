<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\InsuredPerson;

use ArrayIterator;

/**
 * @extends ArrayIterator<int, InsuredPerson>
 */
class InsuredPersonCollection extends ArrayIterator
{
    public function toArray(): array
    {
        $array = [];

        /** @var InsuredPerson $item */
        foreach ($this as $item) {
            $array[] = $item->toArray();
        }

        return $array;
    }
}
