<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\ContractService;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\Contract\ContractId;

readonly class PostgresqlContractServiceFetcher implements ContractServiceFetcherInterface
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
    public function getAll(ContractId $contractId, int $limit, int $skip, Filter $filter): array
    {
        $qb = $this->queryBuilder
            ->table('contract_services')
            ->select(
                [
                    'contract_services.*',
                    'services.name as service_name',
                ]
            )
            ->limit($limit)
            ->skip($skip)
            ->innerJoin('services', 'contract_services.service_id = services.id')
            ->andWhere(['contract_id' => $contractId->getValue()])
            ->orderBy(['services.name']);

        if (!is_null($filter->limitType)) {
            $qb->andWhere(['limit_type' => $filter->limitType]);
        }

        if ($filter->serviceName) {
            $qb->andWhere(["services.name like '%$filter->serviceName%'"]);
        }

        return $qb->fetchAll();
    }
}
