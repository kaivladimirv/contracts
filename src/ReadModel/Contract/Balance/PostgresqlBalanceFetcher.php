<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Balance;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\ReadModel\Contract\Balance\Dto\BalanceDtoCollection;
use App\ReadModel\Contract\Balance\Dto\BalanceDtoConvertor;

readonly class PostgresqlBalanceFetcher implements BalanceFetcherInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private QueryBuilder $queryBuilder, private BalanceDtoConvertor $convertor)
    {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAllByInsuredPersonId(InsuredPersonId $insuredPersonId): BalanceDtoCollection
    {
        $data = $this->queryBuilder
            ->table('balances')
            ->select(
                [
                    'balances.*',
                    'services.name',
                    'insured_persons.contract_id',
                ]
            )
            ->innerJoin('services', 'balances.service_id = services.id')
            ->innerJoin('insured_persons', 'balances.insured_person_id = insured_persons.id')
            ->andWhere(['insured_person_id' => $insuredPersonId->getValue()])
            ->fetchAll();

        return $this->convertor->convertToCollection($data);
    }
}
