<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Event;

use Override;
use App\Model\AbstractDomainEvent;
use App\Model\InsuranceCompany\Entity\AccessToken;
use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use DateTimeImmutable;

class CompanyAccessTokenChangedEvent extends AbstractDomainEvent
{
    public function __construct(
        private readonly InsuranceCompanyId $insuranceCompanyId,
        private readonly ?AccessToken $oldAccessToken,
        private readonly AccessToken $newAccessToken,
        private readonly Email $email
    ) {
        $this->dateOccurred = new DateTimeImmutable();
    }

    public function getOldAccessToken(): ?AccessToken
    {
        return $this->oldAccessToken;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    #[Override]
    public function toArray(): array
    {
        return [
            'insuranceCompanyId' => $this->insuranceCompanyId->getValue(),
            'oldAccessToken'     => !empty($this->oldAccessToken) ? $this->oldAccessToken->getToken() : '',
            'newAccessToken'     => $this->newAccessToken->getToken(),
        ];
    }
}
