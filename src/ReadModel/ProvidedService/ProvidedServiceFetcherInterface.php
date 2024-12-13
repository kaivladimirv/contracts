<?php

declare(strict_types=1);

namespace App\ReadModel\ProvidedService;

use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\ReadModel\ProvidedService\Dto\ExpenseDto;

interface ProvidedServiceFetcherInterface
{
    public function getAllByInsuredPerson(InsuredPersonId $insuredPersonId, int $limit, int $skip, Filter $filter): array;

    public function getQuantityByService(InsuredPersonId $insuredPersonId, ServiceId $serviceId, int $limitType): float;

    public function getAmountByService(InsuredPersonId $insuredPersonId, ServiceId $serviceId, int $limitType): float;

    public function getExpenseByService(InsuredPersonId $insuredPersonId, ServiceId $serviceId, LimitType $limitType): ExpenseDto;

    public function getAmountByInsuredPerson(InsuredPersonId $insuredPersonId): float;

    public function existsForInsuredPerson(InsuredPersonId $insuredPersonId): bool;
}
