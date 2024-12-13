<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\ProvidedService;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\ProvidedService\Id;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Entity\ProvidedService\ProvidedServiceCollection;
use App\Model\Contract\Exception\ProvidedService\ProvidedServiceNotFoundException;
use App\Model\Contract\Service\ProvidedService\ProvidedServiceConvertor;

class PostgresqlProvidedServiceRepository implements ProvidedServiceRepositoryInterface
{
    private const string TABLE_NAME = 'provided_services';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly QueryBuilder $queryBuilder, private readonly ProvidedServiceConvertor $convertor)
    {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(InsuredPersonId $insuredPersonId, int $limit, int $skip): ProvidedServiceCollection
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->limit($limit)
            ->skip($skip)
            ->andWhere(['insured_person_id' => $insuredPersonId->getValue()])
            ->andWhere(['is_deleted' => 0])
            ->orderBy(['date_of_service'])
            ->fetchAll();

        return $this->convertor->convertToCollection($data);
    }

    /**
     * @throws QueryBuilderException
     * @throws ProvidedServiceNotFoundException
     */
    #[Override]
    public function getOne(Id $id): ProvidedService
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['id' => $id->getValue()])
            ->fetch();

        if (!$data) {
            throw new ProvidedServiceNotFoundException('Запись не найдена');
        }

        return $this->convertor->convertToEntity($data);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(ProvidedService $providedService): void
    {
        $data = $providedService->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->insert($data)
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(ProvidedService $providedService): void
    {
        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->update($providedService->toArray())
            ->andWhere(['id' => $providedService->getId()->getValue()])
            ->execute();
    }
}
