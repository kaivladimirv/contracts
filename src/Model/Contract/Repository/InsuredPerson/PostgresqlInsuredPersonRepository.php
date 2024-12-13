<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\InsuredPerson;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonCollection;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\InsuredPerson\PersonId;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonDeleteException;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;
use App\Model\Contract\Service\InsuredPerson\InsuredPersonConvertor;

class PostgresqlInsuredPersonRepository implements InsuredPersonRepositoryInterface
{
    private const string TABLE_NAME = 'insured_persons';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly QueryBuilder $queryBuilder, private readonly InsuredPersonConvertor $convertor)
    {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(ContractId $contractId, int $limit, int $skip): InsuredPersonCollection
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->limit($limit)
            ->skip($skip)
            ->andWhere(['contract_id' => $contractId->getValue()])
            ->orderBy(['policy_number'])
            ->fetchAll();

        return $this->convertor->convertToCollection($data);
    }

    /**
     * @throws QueryBuilderException
     * @throws InsuredPersonNotFoundException
     */
    #[Override]
    public function getOne(InsuredPersonId $insuredPersonId): InsuredPerson
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['id' => $insuredPersonId->getValue()])
            ->fetch();

        if (!$data) {
            throw new InsuredPersonNotFoundException('Застрахованное лицо не найдено');
        }

        return $this->convertor->convertToEntity($data);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(InsuredPerson $insuredPerson): void
    {
        $data = $insuredPerson->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->insert($data)
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(InsuredPerson $insuredPerson): void
    {
        $data = $insuredPerson->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->update($data)
            ->andWhere(['id' => $insuredPerson->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     * @throws InsuredPersonDeleteException
     */
    #[Override]
    public function delete(InsuredPerson $insuredPerson): void
    {
        if (!$insuredPerson->isDeleted()) {
            throw new InsuredPersonDeleteException('Застрахованное лицо не отмечено как удаленное');
        }

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->delete()
            ->andWhere(['id' => $insuredPerson->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByPolicyNumber(ContractId $contractId, string $policyNumber): ?InsuredPerson
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['contract_id' => $contractId->getValue()])
            ->andWhere(['policy_number' => $policyNumber])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByPersonId(ContractId $contractId, PersonId $personId): ?InsuredPerson
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['contract_id' => $contractId->getValue()])
            ->andWhere(['person_id' => $personId->getValue()])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }
}
