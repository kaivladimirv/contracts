<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\ProvidedService;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Model\Contract\Entity\ProvidedService\Id;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Entity\ProvidedService\ProvidedServiceCollection;
use App\Model\Contract\Entity\ProvidedService\Service;
use App\Service\Hydrator\HydratorInterface;

readonly class ProvidedServiceConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    public function convertToEntity(array $data): ProvidedService
    {
        $data = [
            'id'              => new Id($data['id']),
            'contractId'      => new ContractId($data['contract_id']),
            'insuredPersonId' => new InsuredPersonId($data['insured_person_id']),
            'dateOfService'   => $data['date_of_service'],
            'service'         => new Service(
                new ServiceId($data['service_id']),
                $data['service_name'],
                (float) $data['quantity'],
                (float) $data['price'],
                (float) $data['amount']
            ),
            'limitType'       => new LimitType($data['limit_type']),
            'isDeleted'       => (bool) $data['is_deleted'],
            'deletionDate'    => $data['deletion_date'],
        ];

        /* @var ProvidedService $providedService */
        $providedService = $this->hydrator->hydrate(ProvidedService::class, $data);

        return $providedService;
    }

    public function convertToCollection(array $data): ProvidedServiceCollection
    {
        $collection = new ProvidedServiceCollection();

        foreach ($data as $value) {
            $collection->append($this->convertToEntity($value));
        }

        return $collection;
    }
}
