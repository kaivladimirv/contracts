<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\Contract;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\Contract\Contract;
use App\Model\Contract\Entity\Contract\ContractCollection;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\Contract\Exception\Contract\ContractIsNotDeletedException;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\Service\Contract\ContractConvertor;
use Exception;

class PostgresqlContractRepository implements ContractRepositoryInterface
{
    private const string TABLE_NAME = 'contracts';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly QueryBuilder $queryBuilder, private readonly ContractConvertor $convertor)
    {
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function get(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip): ContractCollection
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->limit($limit)
            ->skip($skip)
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->orderBy(['name'])
            ->fetchAll();

        return $this->convertor->convertToCollection($data);
    }

    /**
     * @throws QueryBuilderException
     * @throws ContractNotFoundException
     * @throws Exception
     */
    #[Override]
    public function getOne(ContractId $id): Contract
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['id' => $id->getValue()])
            ->fetch();

        if (!$data) {
            throw new ContractNotFoundException('Договор не найден');
        }

        return $this->convertor->convertToEntity($data);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(Contract $contract): void
    {
        $data = $contract->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->insert($data)
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(Contract $contract): void
    {
        $data = $contract->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->update($data)
            ->andWhere(['id' => $contract->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     * @throws ContractIsNotDeletedException
     */
    #[Override]
    public function delete(Contract $contract): void
    {
        if (!$contract->isDeleted()) {
            throw new ContractIsNotDeletedException('Договор не отмечен как удаленный');
        }

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->delete()
            ->andWhere(['id' => $contract->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function findByNumber(InsuranceCompanyId $insuranceCompanyId, string $number): ?Contract
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->andWhere(['number' => $number])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }
}
