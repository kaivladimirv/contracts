<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\Contract;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;

class CacheItemKeyGenerator
{
    private const string CACHE_PREFIX = 'contracts';

    public function generateFrom(InsuranceCompanyId $insuranceCompanyId, ...$values): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_' . base64_encode(implode('_', $values));
    }

    public function generateFromId(ContractId $id): string
    {
        return self::CACHE_PREFIX . '_' . $id->getValue();
    }

    public function generateFromNumber(InsuranceCompanyId $insuranceCompanyId, string $number): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_by_number_' . base64_encode($number);
    }

    public function generateForLastUpdate(InsuranceCompanyId $insuranceCompanyId): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_last_update';
    }
}
