<?php

declare(strict_types=1);

namespace App\Model\Service\Service;

use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\ServiceId;

class CacheItemKeyGenerator
{
    private const string CACHE_PREFIX = 'services';

    public function generateFrom(InsuranceCompanyId $insuranceCompanyId, ...$values): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_' . base64_encode(implode('_', $values));
    }

    public function generateFromId(ServiceId $id): string
    {
        return self::CACHE_PREFIX . '_' . $id->getValue();
    }

    public function generateFromName(InsuranceCompanyId $insuranceCompanyId, string $name): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_by_name_' . base64_encode($name);
    }

    public function generateForLastUpdate(InsuranceCompanyId $insuranceCompanyId): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_last_update';
    }
}
