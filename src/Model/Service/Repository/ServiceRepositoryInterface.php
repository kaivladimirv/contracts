<?php

declare(strict_types=1);

namespace App\Model\Service\Repository;

use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\Service;
use App\Model\Service\Entity\ServiceCollection;
use App\Model\Service\Entity\ServiceId;
use App\Model\Service\Exception\ServiceNotFoundException;

interface ServiceRepositoryInterface
{
    public function get(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip): ServiceCollection;

    /**
     * @throws ServiceNotFoundException
     */
    public function getOne(ServiceId $id): Service;

    public function add(Service $service): void;

    public function update(Service $service): void;

    public function delete(Service $service): void;

    public function findByName(InsuranceCompanyId $insuranceCompanyId, string $name): ?Service;
}
