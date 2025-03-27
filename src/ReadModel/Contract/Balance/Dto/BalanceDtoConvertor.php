<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Balance\Dto;

use App\Service\Hydrator\HydratorInterface;

readonly class BalanceDtoConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    public function convertToDto(array $data): BalanceDto
    {
        $data = [
            'contractId'      => $data['contract_id'],
            'insuredPersonId' => $data['insured_person_id'],
            'serviceId'       => $data['service_id'],
            'serviceName'     => $data['name'],
            'limitType'       => $data['limit_type'],
            'balance'         => $data['balance'],
        ];

        return $this->hydrator->hydrate(BalanceDto::class, $data);
    }

    public function convertToCollection(array $data): BalanceDtoCollection
    {
        $collection = new BalanceDtoCollection();

        foreach ($data as $value) {
            $collection->append($this->convertToDto($value));
        }

        return $collection;
    }
}
