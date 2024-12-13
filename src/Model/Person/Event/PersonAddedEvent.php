<?php

declare(strict_types=1);

namespace App\Model\Person\Event;

use Override;
use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\NotifierType;
use App\Model\Person\Entity\PersonId;
use App\Model\AbstractDomainEvent;
use App\Model\Person\Entity\PhoneNumber;
use DateTimeImmutable;

class PersonAddedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly PersonId $personId,
        private readonly Name $name,
        private readonly ?Email $email,
        private readonly ?PhoneNumber $phoneNumber,
        private readonly ?NotifierType $notifierType
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'personId'           => $this->personId->getValue(),
            'name'               => $this->name->getFullName(),
            'email'              => !is_null($this->email) ? $this->email->getValue() : null,
            'phoneNumber'        => !is_null($this->phoneNumber) ? $this->phoneNumber->getValue() : null,
            'notifierType'       => !is_null($this->notifierType) ? $this->notifierType->getValue() : null,
        ];
    }
}
