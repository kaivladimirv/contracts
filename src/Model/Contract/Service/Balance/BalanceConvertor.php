<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\Balance;

use App\Model\Contract\Entity\Balance\Balance;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Service\Hydrator\HydratorInterface;

readonly class BalanceConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    public function convertToEntity(array $data): Balance
    {
        $data = [
            'contractId'      => new ContractId($data['contract_id']),
            'insuredPersonId' => new InsuredPersonId($data['insured_person_id']),
            'serviceId'       => new ServiceId($data['service_id']),
            'limitType'       => new LimitType($data['limit_type']),
            'balance'         => $data['balance'],
        ];

        return $this->hydrator->hydrate(Balance::class, $data);
    }
}
