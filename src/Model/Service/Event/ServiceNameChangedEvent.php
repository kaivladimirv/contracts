<?php

declare(strict_types=1);

namespace App\Model\Service\Event;

use Override;
use App\Model\AbstractDomainEvent;
use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\ServiceId;
use DateTimeImmutable;

class ServiceNameChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly ServiceId $id,
        private readonly string $oldName,
        private readonly string $newName
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getInsuranceCompanyId(): InsuranceCompanyId
    {
        return $this->insuranceCompanyId;
    }

    public function getOldName(): string
    {
        return $this->oldName;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'id'                 => $this->id->getValue(),
            'oldName'            => $this->oldName,
            'newName'            => $this->newName,
        ];
    }
}
