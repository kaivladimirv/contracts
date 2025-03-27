<?php

declare(strict_types=1);

namespace App\Model\Service\Service;

use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\ServiceCollection;
use App\Model\Service\Entity\Service;
use App\Model\Service\Entity\ServiceId;
use App\Service\Hydrator\HydratorInterface;

readonly class ServiceConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    public function convertToEntity(array $data): Service
    {
        $data = [
            'id'                 => new ServiceId($data['id']),
            'name'               => $data['name'],
            'insuranceCompanyId' => new InsuranceCompanyId($data['insurance_company_id']),
        ];

        return $this->hydrator->hydrate(Service::class, $data);
    }

    public function convertToCollection(array $data): ServiceCollection
    {
        $collection = new ServiceCollection();

        foreach ($data as $value) {
            $collection->append($this->convertToEntity($value));
        }

        return $collection;
    }
}
