<?php

declare(strict_types=1);

namespace App\Model\Service\Repository;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\Service;
use App\Model\Service\Entity\ServiceCollection;
use App\Model\Service\Entity\ServiceId;
use App\Model\Service\Exception\ServiceIsNotDeletedException;
use App\Model\Service\Exception\ServiceNotFoundException;
use App\Model\Service\Service\ServiceConvertor;

class PostgresqlServiceRepository implements ServiceRepositoryInterface
{
    private const string TABLE_NAME = 'services';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly QueryBuilder $queryBuilder, private readonly ServiceConvertor $convertor)
    {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip): ServiceCollection
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
     * @throws ServiceNotFoundException
     */
    #[Override]
    public function getOne(ServiceId $id): Service
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['id' => $id->getValue()])
            ->fetch();

        if (!$data) {
            throw new ServiceNotFoundException('Услуга не найден');
        }

        return $this->convertor->convertToEntity($data);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(Service $service): void
    {
        $data = $service->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->insert($data)
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(Service $service): void
    {
        $data = $service->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->update($data)
            ->andWhere(['id' => $service->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     * @throws ServiceIsNotDeletedException
     */
    #[Override]
    public function delete(Service $service): void
    {
        if (!$service->isDeleted()) {
            throw new ServiceIsNotDeletedException('Услуга не отмечена как удаленная');
        }

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->delete()
            ->andWhere(['id' => $service->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByName(InsuranceCompanyId $insuranceCompanyId, string $name): ?Service
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->andWhere(['name' => $name])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }
}
