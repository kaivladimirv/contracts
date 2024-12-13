<?php

declare(strict_types=1);

namespace App\Model\Person\Service;

use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Entity\PhoneNumber;

class CacheItemKeyGenerator
{
    private const string CACHE_PREFIX = 'persons';

    public function generateFrom(InsuranceCompanyId $insuranceCompanyId, ...$values): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_' . base64_encode(implode('_', $values));
    }

    public function generateFromId(PersonId $id): string
    {
        return self::CACHE_PREFIX . '_' . $id->getValue();
    }

    public function generateFromEmail(InsuranceCompanyId $insuranceCompanyId, Email $email): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_by_email_' . $email->getValue();
    }

    public function generateFromPhoneNumber(InsuranceCompanyId $insuranceCompanyId, PhoneNumber $phoneNumber): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_by_phone_number_' . $phoneNumber->getValue();
    }

    public function generateFromTelegramUserId(InsuranceCompanyId $insuranceCompanyId, string $telegramUserId): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_by_telegram_user_id_' . $telegramUserId;
    }

    public function generateFromName(InsuranceCompanyId $insuranceCompanyId, Name $name): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_by_name_' . base64_encode(implode(' ', $name->toArray()));
    }

    public function generateForLastUpdate(InsuranceCompanyId $insuranceCompanyId): string
    {
        return self::CACHE_PREFIX . '_' . $insuranceCompanyId->getValue() . '_last_update';
    }
}
