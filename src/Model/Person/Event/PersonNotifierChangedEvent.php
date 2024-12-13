<?php

declare(strict_types=1);

namespace App\Model\Person\Event;

use Override;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\NotifierType;
use App\Model\Person\Entity\PersonId;
use App\Model\AbstractDomainEvent;
use DateTimeImmutable;

class PersonNotifierChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly PersonId $personId,
        private readonly ?NotifierType $oldNotifierType,
        private readonly ?NotifierType $newNotifierType
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'personId'           => $this->personId->getValue(),
            'oldNotifierType'    => $this->oldNotifierType?->getValue(),
            'newNotifierType'    => $this->newNotifierType?->getValue(),
        ];
    }
}
