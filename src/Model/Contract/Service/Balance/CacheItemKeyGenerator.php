<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\Balance;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;

class CacheItemKeyGenerator
{
    private const string CACHE_PREFIX = 'balance';

    public function generateFrom(InsuredPersonId $insuredPersonId, ...$values): string
    {
        return self::CACHE_PREFIX . '_' . $insuredPersonId->getValue() . '_' . base64_encode(implode('_', $values));
    }

    public function generateFromId(InsuredPersonId $insuredPersonId, ServiceId $serviceId, LimitType $limitType): string
    {
        return self::CACHE_PREFIX . '_' . $insuredPersonId->getValue() . '_service_' . $serviceId->getValue() . '_limit_type_' . $limitType->getValue();
    }

    public function generateFromContractId(ContractId $contractId): string
    {
        return self::CACHE_PREFIX . '_by_contract_' . $contractId->getValue();
    }

    public function generateForLastUpdate(InsuredPersonId $insuredPersonId): string
    {
        return self::CACHE_PREFIX . '_' . $insuredPersonId->getValue() . '_last_update';
    }

    public function generateForLastUpdateByContract(ContractId $contractId): string
    {
        return self::CACHE_PREFIX . '_by_contract_' . $contractId->getValue() . '_last_update';
    }
}
