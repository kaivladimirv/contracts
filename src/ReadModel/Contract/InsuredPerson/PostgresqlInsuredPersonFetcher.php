<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\InsuredPerson;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\Contract\ContractId;

readonly class PostgresqlInsuredPersonFetcher implements InsuredPersonFetcherInterface
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
            ->table('insured_persons')
            ->select(
                [
                    'insured_persons.*',
                    "persons.last_name || ' ' || persons.first_name || ' ' || persons.middle_name as person_name",
                ]
            )
            ->limit($limit)
            ->skip($skip)
            ->innerJoin('persons', 'insured_persons.person_id = persons.id')
            ->andWhere(['contract_id' => $contractId->getValue()])
            ->orderBy(['person_name']);

        if ($filter->policyNumber) {
            $qb->andWhere(['policy_number' => $filter->policyNumber]);
        }

        if ($filter->personName) {
            $partsName = explode(' ', trim($filter->personName));

            $qb->andWhere(["last_name like '$partsName[0]%'"]);

            if (!empty($partsName[1])) {
                $qb->andWhere(["first_name like '$partsName[1]%'"]);
            }

            if (!empty($partsName[2])) {
                $qb->andWhere(["middle_name like '$partsName[2]%'"]);
            }
        }

        if (!is_null($filter->isAllowedToExceedLimit)) {
            $qb->andWhere(['is_allowed_to_exceed_limit' => $filter->isAllowedToExceedLimit ? 1 : 0]);
        }

        return $qb->fetchAll();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAllIds(ContractId $contractId): array
    {
        $data = $this->queryBuilder
            ->table('insured_persons')
            ->select(['id'])
            ->andWhere(['contract_id' => $contractId->getValue()])
            ->fetchAll();

        return array_column($data, 'id');
    }
}
