<?php

declare(strict_types=1);

namespace App\Model\Person\Event;

use Override;
use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\PersonId;
use App\Model\AbstractDomainEvent;
use DateTimeImmutable;

class PersonEmailChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly PersonId $personId,
        private readonly ?Email $oldEmail,
        private readonly ?Email $newEmail
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getInsuranceCompanyId(): InsuranceCompanyId
    {
        return $this->insuranceCompanyId;
    }

    public function getOldEmail(): ?Email
    {
        return $this->oldEmail;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'personId'           => $this->personId->getValue(),
            'oldEmail'           => $this->oldEmail ? $this->oldEmail->getValue() : '',
            'newEmail'           => $this->newEmail ? $this->newEmail->getValue() : '',
        ];
    }
}
