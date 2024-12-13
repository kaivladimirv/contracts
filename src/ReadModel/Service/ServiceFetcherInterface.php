<?php

declare(strict_types=1);

namespace App\ReadModel\Service;

use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\ServiceId;

interface ServiceFetcherInterface
{
    public function getAll(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip, Filter $filter): array;

    public function isExist(InsuranceCompanyId $insuranceCompanyId, ServiceId $id): bool;
}
