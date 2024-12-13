<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Debtor;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Contract\Entity\Contract\ContractId;
use App\ReadModel\Contract\Debtor\Dto\DebtorDtoCollection;
use App\ReadModel\Contract\Debtor\Dto\DebtorDtoConvertor;

readonly class PostgresqlDebtorFetcher implements DebtorFetcherInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private QueryBuilder $queryBuilder, private DebtorDtoConvertor $convertor)
    {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(ContractId $contractId): DebtorDtoCollection
    {
        $data = $this->queryBuilder
            ->table('balances')
            ->select(
                [
                    'balances.insured_person_id',
                    'balances.service_id',
                    'abs(balances.balance) as debt',
                    'persons.id as person_id',
                    'persons.last_name',
                    'persons.first_name',
                    'persons.middle_name',
                    'services.name as service_name',
                ]
            )
            ->innerJoin('insured_persons', 'balances.insured_person_id = insured_persons.id')
            ->innerJoin('persons', 'insured_persons.person_id = persons.id')
            ->innerJoin('services', 'balances.service_id = services.id')
            ->andWhere(['balances.contract_id' => $contractId->getValue()])
            ->andWhere(['balances.balance < 0'])
            ->fetchAll();

        return $this->convertor->convertToCollection($data);
    }
}
