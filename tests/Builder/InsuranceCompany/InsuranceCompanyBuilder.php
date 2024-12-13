<?php

declare(strict_types=1);

namespace App\Tests\Builder\InsuranceCompany;

use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Exception\EmailConfirmTokenNotSpecifiedException;
use App\Model\InsuranceCompany\Exception\IncorrectConfirmTokenException;
use App\Model\InsuranceCompany\Exception\PasswordNotSpecifiedException;

class InsuranceCompanyBuilder
{
    private ?InsuranceCompanyId $id;
    private ?string $name = 'Company test';
    private ?Email $email;
    private ?string $passwordHash = 'hash';
    private ?string $emailConfirmToken = 'token';
    private bool $isEmailConfirmed = false;

    public function __construct()
    {
        $this->id = InsuranceCompanyId::next();
        $this->email = new Email('company@app.test');
    }

    public function confirmed(): self
    {
        $clone = clone $this;
        $clone->isEmailConfirmed = true;

        return $clone;
    }

    /**
     * @throws IncorrectConfirmTokenException
     * @throws EmailConfirmTokenNotSpecifiedException
     * @throws PasswordNotSpecifiedException
     */
    public function build(): InsuranceCompany
    {
        $insuranceCompany = InsuranceCompany::register(
            $this->id,
            $this->name,
            $this->email,
            $this->passwordHash,
            $this->emailConfirmToken
        );

        if ($this->isEmailConfirmed) {
            $insuranceCompany->confirmRegister($this->emailConfirmToken);
        }

        return $insuranceCompany;
    }
}
