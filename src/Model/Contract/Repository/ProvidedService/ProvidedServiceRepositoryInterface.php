<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\ProvidedService;

use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\ProvidedService\Id;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Entity\ProvidedService\ProvidedServiceCollection;

interface ProvidedServiceRepositoryInterface
{
    public function get(InsuredPersonId $insuredPersonId, int $limit, int $skip): ProvidedServiceCollection;

    public function getOne(Id $id): ProvidedService;

    public function add(ProvidedService $providedService): void;

    public function update(ProvidedService $providedService): void;
}
