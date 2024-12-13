<?php

declare(strict_types=1);

namespace App\Model\Service\Entity;

use ArrayIterator;

/**
 * @extends ArrayIterator<int, Service>
 */
class ServiceCollection extends ArrayIterator
{
    public function toArray(): array
    {
        $array = [];

        /** @var Service $item */
        foreach ($this as $item) {
            $array[] = $item->toArray();
        }

        return $array;
    }
}
