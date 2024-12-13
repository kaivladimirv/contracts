<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Debtor\Dto;

class DebtorDto
{
    public string $insuredPersonId;
    public string $personId;
    public string $personLastName;
    public string $personFirstName;
    public string $personMiddleName;
    public string $serviceId;
    public string $serviceName;
    public float $debt;

    public function toArray(): array
    {
        return [
            'insured_person_id' => $this->insuredPersonId,
            'person_id'         => $this->personId,
            'last_name'         => $this->personLastName,
            'first_name'        => $this->personFirstName,
            'middle_name'       => $this->personMiddleName,
            'service_id'        => $this->serviceId,
            'service_name'      => $this->serviceName,
            'debt'              => $this->debt,
        ];
    }
}
