<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Event;

use Override;
use App\Model\AbstractDomainEvent;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use DateTimeImmutable;

class CompanyNameChangedEvent extends AbstractDomainEvent
{
    public function __construct(private readonly InsuranceCompanyId $insuranceCompanyId, private readonly string $oldName, private readonly string $newName)
    {
        $this->dateOccurred = new DateTimeImmutable();
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
            'oldName'            => $this->oldName,
            'newName'            => $this->newName,
        ];
    }
}
