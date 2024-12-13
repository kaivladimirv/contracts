<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\ProvidedService;

use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\ProvidedService\Id;

class CacheItemKeyGenerator
{
    private const string CACHE_PREFIX = 'provided_services';

    public function generateFrom(InsuredPersonId $insuredPersonId, ...$values): string
    {
        return self::CACHE_PREFIX . '_' . $insuredPersonId->getValue() . '_' . base64_encode(implode('_', $values));
    }

    public function generateFromId(Id $id): string
    {
        return self::CACHE_PREFIX . $id->getValue();
    }

    public function generateForLastUpdate(InsuredPersonId $insuredPersonId): string
    {
        return self::CACHE_PREFIX . '_' . $insuredPersonId->getValue() . '_last_update';
    }
}
