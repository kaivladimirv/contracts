<?php

declare(strict_types=1);

namespace App\Model\Person\Event;

use Override;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Entity\PhoneNumber;
use App\Model\AbstractDomainEvent;
use DateTimeImmutable;

class PersonPhoneNumberChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly PersonId $personId,
        private readonly ?PhoneNumber $oldPhoneNumber,
        private readonly ?PhoneNumber $newPhoneNumber
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getInsuranceCompanyId(): InsuranceCompanyId
    {
        return $this->insuranceCompanyId;
    }

    public function getOldPhoneNumber(): ?PhoneNumber
    {
        return $this->oldPhoneNumber;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'personId'           => $this->personId->getValue(),
            'oldPhoneNumber'     => $this->oldPhoneNumber ? $this->oldPhoneNumber->getValue() : '',
            'newPhoneNumber'     => $this->newPhoneNumber ? $this->newPhoneNumber->getValue() : '',
        ];
    }
}
