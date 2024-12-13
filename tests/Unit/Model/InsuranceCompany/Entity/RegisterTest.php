<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\InsuranceCompany\Entity;

use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Exception\EmailConfirmTokenNotSpecifiedException;
use App\Model\InsuranceCompany\Exception\NameNotSpecifiedException;
use App\Model\InsuranceCompany\Exception\PasswordNotSpecifiedException;
use PHPUnit\Framework\TestCase;

class RegisterTest extends TestCase
{
    /**
     * @throws EmailConfirmTokenNotSpecifiedException
     * @throws PasswordNotSpecifiedException
     */
    public function testSuccess(): void
    {
        $insuranceCompany = InsuranceCompany::register(
            $id = InsuranceCompanyId::next(),
            $name = 'Company test',
            $email = new Email('company@app.test'),
            $passwordHash = 'hash',
            $emailConfirmToken = 'token'
        );

        self::assertEquals($id, $insuranceCompany->getId());
        self::assertEquals($name, $insuranceCompany->getName());
        self::assertEquals($email, $insuranceCompany->getEmail());
        self::assertEquals($passwordHash, $insuranceCompany->getPasswordHash());
        self::assertEquals($emailConfirmToken, $insuranceCompany->getEmailConfirmToken());
    }

    /**
     * @throws PasswordNotSpecifiedException
     * @throws EmailConfirmTokenNotSpecifiedException
     */
    public function testNameNotSpecified(): void
    {
        self::expectException(NameNotSpecifiedException::class);

        InsuranceCompany::register(
            InsuranceCompanyId::next(),
            '',
            new Email('company@app.test'),
            'hash',
            $emailConfirmToken = 'token'
        );
    }

    /**
     * @throws PasswordNotSpecifiedException
     * @throws NameNotSpecifiedException
     */
    public function testEmailConfirmTokenNotSpecified(): void
    {
        self::expectException(EmailConfirmTokenNotSpecifiedException::class);

        InsuranceCompany::register(
            InsuranceCompanyId::next(),
            'Company test',
            new Email('company@app.test'),
            'hash',
            $emailConfirmToken = ''
        );
    }

    /**
     * @throws EmailConfirmTokenNotSpecifiedException
     * @throws NameNotSpecifiedException
     */
    public function testPasswordNotSpecified(): void
    {
        self::expectException(PasswordNotSpecifiedException::class);

        InsuranceCompany::register(
            InsuranceCompanyId::next(),
            'Company test',
            new Email('company@app.test'),
            '',
            $emailConfirmToken = 'token'
        );
    }
}
