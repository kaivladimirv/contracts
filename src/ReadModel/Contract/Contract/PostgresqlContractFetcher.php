<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Contract;

use App\Model\Contract\Entity\Contract\ContractId;
use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\ReadModel\Contract\Contract\Filter\Filter;

readonly class PostgresqlContractFetcher implements ContractFetcherInterface
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
            ->table('contracts')
            ->select(['*'])
            ->limit($limit)
            ->skip($skip)
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->orderBy(['start_date']);

        if ($filter->number) {
            $qb->andWhere(['number' => $filter->number]);
        }

        return $qb->fetchAll();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function isExist(InsuranceCompanyId $insuranceCompanyId, ContractId $contractId): bool
    {
        return $this->queryBuilder
            ->table('contracts')
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->andWhere(['id' => $contractId->getValue()])
            ->count('id') > 0;
    }
}
