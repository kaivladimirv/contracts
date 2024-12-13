<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Debtor\Dto;

use App\Service\Hydrator\HydratorInterface;

readonly class DebtorDtoConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    public function convertToDto(array $data): DebtorDto
    {
        $data = [
            'insuredPersonId'  => $data['insured_person_id'],
            'personId'         => $data['person_id'],
            'personLastName'   => $data['last_name'],
            'personFirstName'  => $data['first_name'],
            'personMiddleName' => $data['middle_name'],
            'serviceId'        => $data['service_id'],
            'serviceName'      => $data['service_name'],
            'debt'             => $data['debt'],
        ];

        /* @var DebtorDto $debtorDto */
        $debtorDto = $this->hydrator->hydrate(DebtorDto::class, $data);

        return $debtorDto;
    }

    public function convertToCollection(array $data): DebtorDtoCollection
    {
        $collection = new DebtorDtoCollection();

        foreach ($data as $value) {
            $collection->append($this->convertToDto($value));
        }

        return $collection;
    }
}
