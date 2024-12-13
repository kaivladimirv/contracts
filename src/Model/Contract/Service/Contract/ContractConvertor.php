<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\Contract;

use App\Model\Contract\Entity\Contract\Contract;
use App\Model\Contract\Entity\Contract\ContractCollection;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\Contract\Entity\Contract\Period;
use App\Service\Hydrator\HydratorInterface;
use DateTimeImmutable;
use Exception;

readonly class ContractConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    /**
     * @throws Exception
     */
    public function convertToEntity(array $data): Contract
    {
        $data = [
            'id'                 => new ContractId($data['id']),
            'number'             => $data['number'],
            'insuranceCompanyId' => new InsuranceCompanyId($data['insurance_company_id']),
            'period'             => new Period(new DateTimeImmutable($data['start_date']), new DateTimeImmutable($data['end_date'])),
            'maxAmount'          => $data['max_amount'],
        ];

        /* @var Contract $contract */
        $contract = $this->hydrator->hydrate(Contract::class, $data);

        return $contract;
    }

    /**
     * @throws Exception
     */
    public function convertToCollection(array $data): ContractCollection
    {
        $collection = new ContractCollection();

        foreach ($data as $value) {
            $collection->append($this->convertToEntity($value));
        }

        return $collection;
    }
}
