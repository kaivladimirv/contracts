<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Balance\Dto;

class BalanceDto
{
    public string $contractId;
    public string $insuredPersonId;
    public string $serviceId;
    public string $serviceName;
    public int $limitType;
    public float $balance;

    public function toArray(): array
    {
        return [
            'contract_id'       => $this->contractId,
            'insured_person_id' => $this->insuredPersonId,
            'service_id'        => $this->serviceId,
            'name'              => $this->serviceName,
            'limit_type'        => $this->limitType,
            'balance'           => $this->balance,
        ];
    }

    public function only(array $keys): array
    {
        return array_filter(
            $this->toArray(),
            fn ($key) => in_array($key, $keys),
            ARRAY_FILTER_USE_KEY
        );
    }
}
