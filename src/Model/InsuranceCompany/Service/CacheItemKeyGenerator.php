<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Service;

use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;

class CacheItemKeyGenerator
{
    private const string CACHE_PREFIX = 'insurance_companies';

    public function generateFrom(...$values): string
    {
        return self::CACHE_PREFIX . '_' . implode('_', $values);
    }

    public function generateFromId(InsuranceCompanyId $id): string
    {
        return self::CACHE_PREFIX . '_' . $id->getValue();
    }

    public function generateFromAccessToken(string $accessToken): string
    {
        return self::CACHE_PREFIX . '_by_access_token_' . $accessToken;
    }

    public function generateFromEmail(string $email): string
    {
        return self::CACHE_PREFIX . '_by_email_' . $email;
    }

    public function generateFromName(string $name): string
    {
        return self::CACHE_PREFIX . '_by_name_' . base64_encode($name);
    }

    public function generateForLastUpdate(): string
    {
        return self::CACHE_PREFIX . '_last_update';
    }
}
