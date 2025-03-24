<?php

declare(strict_types=1);

namespace App\ReadModel\Person;

use App\Model\Person\Entity\PersonId;
use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Person\Entity\InsuranceCompanyId;

readonly class PostgresqlPersonFetcher implements PersonFetcherInterface
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
            ->table('persons')
            ->select(['*'])
            ->limit($limit)
            ->skip($skip)
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->orderBy(
                [
                    'last_name',
                    'first_name',
                    'middle_name',
                ]
            );

        if ($filter->name) {
            $partsName = explode(' ', trim($filter->name));

            $qb->andWhere(["last_name like '$partsName[0]%'"]);

            if (!empty($partsName[1])) {
                $qb->andWhere(["first_name like '$partsName[1]%'"]);
            }

            if (!empty($partsName[2])) {
                $qb->andWhere(["middle_name like '$partsName[2]%'"]);
            }
        }

        if ($filter->email) {
            $qb->andWhere(['email' => $filter->email]);
        }

        if ($filter->phoneNumber) {
            $qb->andWhere(['phone_number' => $filter->phoneNumber]);
        }

        return $qb->fetchAll();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function isExist(InsuranceCompanyId $insuranceCompanyId, PersonId $id): bool
    {
        return $this->queryBuilder
                ->table('persons')
                ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
                ->andWhere(['id' => $id->getValue()])
                ->count('id') > 0;
    }
}
