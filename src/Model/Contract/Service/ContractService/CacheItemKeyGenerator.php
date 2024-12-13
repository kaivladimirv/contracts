<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\ContractService;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;

class CacheItemKeyGenerator
{
    private const string CACHE_PREFIX = 'contract_services';

    public function generateFrom(ContractId $contractId, ...$values): string
    {
        return self::CACHE_PREFIX . '_' . $contractId->getValue() . '_' . base64_encode(implode('_', $values));
    }

    public function generateFromId(ContractId $contractId, ServiceId $serviceId): string
    {
        return self::CACHE_PREFIX . '_contract_' . $contractId->getValue() . '_service_' . $serviceId->getValue();
    }

    public function generateForLastUpdate(ContractId $contractId): string
    {
        return self::CACHE_PREFIX . '_' . $contractId->getValue() . '_last_update';
    }
}
