<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Event;

use Override;
use App\Model\AbstractDomainEvent;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use DateTimeImmutable;

class CompanyRegistrationConfirmedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly string $emailConfirmToken
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getInsuranceCompanyId(): InsuranceCompanyId
    {
        return $this->insuranceCompanyId;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'emailConfirmToken'  => $this->emailConfirmToken,
        ];
    }
}
