<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Entity;

use ArrayIterator;

/**
 * @extends ArrayIterator<int, InsuranceCompany>
 */
class InsuranceCompanyCollection extends ArrayIterator
{
    public function toArray(): array
    {
        $array = [];

        /** @var InsuranceCompany $insuranceCompany */
        foreach ($this as $insuranceCompany) {
            $array[] = $insuranceCompany->toArray();
        }

        return $array;
    }
}
