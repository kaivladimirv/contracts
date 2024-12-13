<?php

declare(strict_types=1);

namespace App\ReadModel\Service;

use App\Model\Service\Entity\ServiceId;
use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Service\Entity\InsuranceCompanyId;

readonly class PostgresqlServiceFetcher implements ServiceFetcherInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private QueryBuilder $queryBuilder)
    {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAll(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip, Filter $filter): array
    {
        $qb = $this->queryBuilder
            ->table('services')
            ->select(['*'])
            ->limit($limit)
            ->skip($skip)
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->orderBy(['name']);

        if ($filter->name) {
            $qb->andWhere(["name like '%$filter->name%'"]);
        }

        return $qb->fetchAll();
    }

    /**
     * @throws QueryBuilderException
     */
    public function isExist(InsuranceCompanyId $insuranceCompanyId, ServiceId $id): bool
    {
        return $this->queryBuilder
                ->table('services')
                ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
                ->andWhere(['id' => $id->getValue()])
                ->count('id') > 0;
    }
}
