<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\ContractService;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ContractService;
use App\Model\Contract\Entity\ContractService\ContractServiceCollection;
use App\Model\Contract\Entity\ContractService\ContractServiceId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\Limit\Limit;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Service\Hydrator\HydratorInterface;

readonly class ContractServiceConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    public function convertToEntity(array $data): ContractService
    {
        $data = [
            'id' => new ContractServiceId($data['id']),
            'contractId' => new ContractId($data['contract_id']),
            'serviceId' => new ServiceId($data['service_id']),
            'limit' => new Limit(new LimitType($data['limit_type']), floatval($data['limit_value'])),
        ];

        /* @var ContractService $contractService */
        $contractService = $this->hydrator->hydrate(ContractService::class, $data);

        return $contractService;
    }

    public function convertToCollection(array $data): ContractServiceCollection
    {
        $collection = new ContractServiceCollection();

        foreach ($data as $value) {
            $collection->append($this->convertToEntity($value));
        }

        return $collection;
    }
}
