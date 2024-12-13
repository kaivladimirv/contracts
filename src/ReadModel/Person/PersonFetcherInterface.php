<?php

declare(strict_types=1);

namespace App\ReadModel\Person;

use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\PersonId;

interface PersonFetcherInterface
{
    public function getAll(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip, Filter $filter): array;

    public function isExist(InsuranceCompanyId $insuranceCompanyId, PersonId $id): bool;
}
