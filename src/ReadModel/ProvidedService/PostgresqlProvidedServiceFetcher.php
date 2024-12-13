<?php

declare(strict_types=1);

namespace App\ReadModel\ProvidedService;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\ReadModel\ProvidedService\Dto\ExpenseDto;
use App\ReadModel\ProvidedService\Dto\ExpenseDtoConvertor;

readonly class PostgresqlProvidedServiceFetcher implements ProvidedServiceFetcherInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private QueryBuilder $queryBuilder, private ExpenseDtoConvertor $convertor)
    {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAllByInsuredPerson(
        InsuredPersonId $insuredPersonId,
        int $limit,
        int $skip,
        Filter $filter
    ): array {
        $qb = $this->queryBuilder
            ->table('provided_services')
            ->select(['*'])
            ->limit($limit)
            ->skip($skip)
            ->andWhere(['insured_person_id' => $insuredPersonId->getValue()])
            ->andWhere(['is_deleted' => 0])
            ->orderBy(['date_of_service']);

        if ($filter->serviceName) {
            $qb->andWhere(["service_name like '%$filter->serviceName%'"]);
        }

        if ($filter->startDate and $filter->endDate) {
            $qb->andWhere(["date_of_service BETWEEN '$filter->startDate' and '$filter->endDate'"]);
        } elseif ($filter->startDate) {
            $qb->andWhere(["date_of_service >= '$filter->startDate'"]);
        } elseif ($filter->endDate) {
            $qb->andWhere(["date_of_service <= '$filter->endDate'"]);
        }

        return $qb->fetchAll();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getQuantityByService(InsuredPersonId $insuredPersonId, ServiceId $serviceId, int $limitType): float
    {
        return (float) $this->queryBuilder
            ->table('provided_services')
            ->andWhere(['insured_person_id' => $insuredPersonId->getValue()])
            ->andWhere(['service_id' => $serviceId->getValue()])
            ->andWhere(['limit_type' => $limitType])
            ->andWhere(['is_deleted' => 0])
            ->sum('quantity');
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAmountByService(InsuredPersonId $insuredPersonId, ServiceId $serviceId, int $limitType): float
    {
        return (float) $this->queryBuilder
            ->table('provided_services')
            ->andWhere(['insured_person_id' => $insuredPersonId->getValue()])
            ->andWhere(['service_id' => $serviceId->getValue()])
            ->andWhere(['limit_type' => $limitType])
            ->andWhere(['is_deleted' => 0])
            ->sum('amount');
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAmountByInsuredPerson(InsuredPersonId $insuredPersonId): float
    {
        return (float) $this->queryBuilder
            ->table('provided_services')
            ->andWhere(['insured_person_id' => $insuredPersonId->getValue()])
            ->andWhere(['is_deleted' => 0])
            ->sum('amount');
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getExpenseByService(InsuredPersonId $insuredPersonId, ServiceId $serviceId, LimitType $limitType): ExpenseDto
    {
        $data = $this->queryBuilder
            ->table('provided_services')
            ->select(
                [
                    'sum(quantity) as quantity',
                    'sum(amount) as amount',
                ]
            )
            ->andWhere(['insured_person_id' => $insuredPersonId->getValue()])
            ->andWhere(['service_id' => $serviceId->getValue()])
            ->andWhere(['limit_type' => $limitType->getValue()])
            ->andWhere(['is_deleted' => 0])
            ->fetch();

        return $this->convertor->convertToDto($data);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function existsForInsuredPerson(InsuredPersonId $insuredPersonId): bool
    {
        return $this->queryBuilder
            ->table('provided_services')
            ->andWhere(['insured_person_id' => $insuredPersonId->getValue()])
            ->andWhere(['is_deleted' => 0])
            ->count('id') > 0;
    }
}
