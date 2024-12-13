<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\ProvidedService;

use ArrayIterator;

/**
 * @extends ArrayIterator<int, ProvidedService>
 */
class ProvidedServiceCollection extends ArrayIterator
{
    public function toArray(): array
    {
        $array = [];

        /** @var ProvidedService $item */
        foreach ($this as $item) {
            $array[] = $item->toArray();
        }

        return $array;
    }
}
