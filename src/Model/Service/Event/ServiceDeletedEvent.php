<?php

declare(strict_types=1);

namespace App\Model\Service\Event;

use Override;
use App\Model\AbstractDomainEvent;
use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\ServiceId;
use DateTimeImmutable;

class ServiceDeletedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly ServiceId $id,
        private readonly string $name
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'id'                 => $this->id->getValue(),
            'name'               => $this->name,
        ];
    }
}
