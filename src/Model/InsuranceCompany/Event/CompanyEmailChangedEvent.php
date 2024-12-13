<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Event;

use Override;
use App\Model\AbstractDomainEvent;
use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use DateTimeImmutable;

class CompanyEmailChangedEvent extends AbstractDomainEvent
{
    public function __construct(private readonly InsuranceCompanyId $insuranceCompanyId, private readonly Email $oldEmail, private readonly Email $newEmail)
    {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getOldEmail(): Email
    {
        return $this->oldEmail;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'oldEmail'           => $this->oldEmail->getValue(),
            'newEmail'           => $this->newEmail->getValue(),
        ];
    }
}
