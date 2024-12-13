<?php

declare(strict_types=1);

namespace App\Model\Person\Event;

use Override;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\PersonId;
use App\Model\AbstractDomainEvent;
use DateTimeImmutable;

class PersonNameChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly PersonId $personId,
        private readonly Name $oldName,
        private readonly Name $newName
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getInsuranceCompanyId(): InsuranceCompanyId
    {
        return $this->insuranceCompanyId;
    }

    public function getOldName(): Name
    {
        return $this->oldName;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'personId'           => $this->personId->getValue(),
            'oldName'            => $this->oldName->getFullName(),
            'newName'            => $this->newName->getFullName(),
        ];
    }
}
