<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\Balance;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\Balance\Balance;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Model\Contract\Service\Balance\BalanceConvertor;
use App\Model\Contract\Exception\Balance\BalanceNotFoundException;

class PostgresqlBalanceRepository implements BalanceRepositoryInterface
{
    private const string TABLE_NAME = 'balances';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly QueryBuilder $queryBuilder, private readonly BalanceConvertor $balanceConvertor)
    {
    }

    /**
     * @throws QueryBuilderException
     * @throws BalanceNotFoundException
     */
    #[Override]
    public function getOne(InsuredPersonId $insuredPersonId, ServiceId $serviceId, LimitType $limitType): Balance
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['insured_person_id' => $insuredPersonId->getValue()])
            ->andWhere(['service_id' => $serviceId->getValue()])
            ->andWhere(['limit_type' => $limitType->getValue()])
            ->fetch();

        if (!$data) {
            throw new BalanceNotFoundException('Остатки не заполнены');
        }

        return $this->balanceConvertor->convertToEntity($data);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(Balance $balance): void
    {
        $data = $balance->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->insert($data)
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(Balance $balance): void
    {
        $data = $balance->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->update($data)
            ->andWhere(['insured_person_id' => $balance->getInsuredPersonId()->getValue()])
            ->andWhere(['service_id' => $balance->getServiceId()->getValue()])
            ->andWhere(['limit_type' => $balance->getLimitType()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function save(Balance $balance): void
    {
        $data = $balance->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->updateOrInsert(
                $data,
                $data,
                [
                    'insured_person_id',
                    'service_id',
                    'limit_type',
                ]
            )
            ->execute();
    }
}
