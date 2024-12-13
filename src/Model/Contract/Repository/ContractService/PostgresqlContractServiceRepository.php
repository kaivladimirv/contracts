<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\ContractService;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ContractService;
use App\Model\Contract\Entity\ContractService\ContractServiceCollection;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Exception\ContractService\ContractServiceIsNotDeletedException;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;
use App\Model\Contract\Service\ContractService\ContractServiceConvertor;

class PostgresqlContractServiceRepository implements ContractServiceRepositoryInterface
{
    private const string TABLE_NAME = 'contract_services';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly QueryBuilder $queryBuilder, private readonly ContractServiceConvertor $convertor)
    {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAll(ContractId $contractId): ContractServiceCollection
    {
        return $this->get($contractId, 0, 0);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(ContractId $contractId, int $limit, int $skip): ContractServiceCollection
    {
        $builder = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*']);

        if ($limit !== 0) {
            $builder = $builder->limit($limit)->skip($skip);
        }

        $data = $builder
            ->andWhere(['contract_id' => $contractId->getValue()])
            ->fetchAll();

        return $this->convertor->convertToCollection($data);
    }

    /**
     * @throws QueryBuilderException
     * @throws ContractServiceNotFoundException
     */
    #[Override]
    public function getOne(ContractId $contractId, ServiceId $serviceId): ContractService
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['contract_id' => $contractId->getValue()])
            ->andWhere(['service_id' => $serviceId->getValue()])
            ->fetch();

        if (!$data) {
            throw new ContractServiceNotFoundException('Услуга не найдена в договоре');
        }

        return $this->convertor->convertToEntity($data);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(ContractService $contractService): void
    {
        $data = $contractService->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->insert($data)
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(ContractService $contractService): void
    {
        $data = $contractService->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->update($data)
            ->andWhere(['id' => $contractService->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     * @throws ContractServiceIsNotDeletedException
     */
    #[Override]
    public function delete(ContractService $contractService): void
    {
        if (!$contractService->isDeleted()) {
            throw new ContractServiceIsNotDeletedException('Услуга не отмечена как удаленная');
        }

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->delete()
            ->andWhere(['id' => $contractService->getId()->getValue()])
            ->execute();
    }
}
