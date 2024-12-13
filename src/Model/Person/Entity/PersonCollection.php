<?php

declare(strict_types=1);

namespace App\Model\Person\Entity;

use ArrayIterator;

/**
 * @extends ArrayIterator<int, Person>
 */
class PersonCollection extends ArrayIterator
{
    public function toArray(): array
    {
        $array = [];

        /** @var Person $item */
        foreach ($this as $item) {
            $array[] = $item->toArray();
        }

        return $array;
    }
}
