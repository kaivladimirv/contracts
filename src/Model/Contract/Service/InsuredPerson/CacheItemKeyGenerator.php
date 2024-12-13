<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\InsuredPerson;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\InsuredPerson\PersonId;

class CacheItemKeyGenerator
{
    private const string CACHE_PREFIX = 'insured_persons';

    public function generateFrom(ContractId $contractId, ...$values): string
    {
        return self::CACHE_PREFIX . '_' . $contractId->getValue() . '_' . base64_encode(implode('_', $values));
    }

    public function generateFromId(InsuredPersonId $insuredPersonId): string
    {
        return self::CACHE_PREFIX . '_' . $insuredPersonId->getValue();
    }

    public function generateFromPolicyNumber(ContractId $contractId, string $policyNumber): string
    {
        return self::CACHE_PREFIX . '_' . $contractId->getValue() . '_by_policy_number_' . base64_encode($policyNumber);
    }

    public function generateFromPerson(ContractId $contractId, PersonId $personId): string
    {
        return self::CACHE_PREFIX . '_' . $contractId->getValue() . '_by_person_' . $personId->getValue();
    }

    public function generateForLastUpdate(ContractId $contractId): string
    {
        return self::CACHE_PREFIX . '_' . $contractId->getValue() . '_last_update';
    }
}
