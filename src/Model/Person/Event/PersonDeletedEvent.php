<?php

declare(strict_types=1);

namespace App\Model\Person\Event;

use Override;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\PersonId;
use App\Model\AbstractDomainEvent;
use DateTimeImmutable;

class PersonDeletedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly PersonId $personId,
        private readonly Name $name
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
        ];
    }
}
