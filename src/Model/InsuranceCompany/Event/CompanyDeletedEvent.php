<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Event;

use Override;
use App\Model\AbstractDomainEvent;
use DateTimeImmutable;

class CompanyDeletedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly string $insuranceCompanyId,
        private readonly string $name,
        private readonly string $email
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId,
            'name'               => $this->name,
            'email'              => $this->email,
        ];
    }
}
