<?php

declare(strict_types=1);

namespace Unit\Model\InsuranceCompany\Entity;

use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Exception\EmailConfirmTokenNotSpecifiedException;
use App\Model\InsuranceCompany\Exception\IncorrectConfirmTokenException;
use App\Model\InsuranceCompany\Exception\NameNotSpecifiedException;
use App\Model\InsuranceCompany\Exception\PasswordNotSpecifiedException;
use App\Tests\Builder\InsuranceCompany\InsuranceCompanyBuilder;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    /**
     * @throws IncorrectConfirmTokenException
     * @throws EmailConfirmTokenNotSpecifiedException
     * @throws NameNotSpecifiedException
     * @throws PasswordNotSpecifiedException
     */
    public function testChangeNameSuccess(): void
    {
        $insuranceCompany = (new InsuranceCompanyBuilder())->build();

        $insuranceCompany->changeName($name = 'new name');

        self::assertEquals($name, $insuranceCompany->getName());
    }


    /**
     * @throws IncorrectConfirmTokenException
     * @throws EmailConfirmTokenNotSpecifiedException
     * @throws PasswordNotSpecifiedException
     */
    public function testNameNotSpecified(): void
    {
        $insuranceCompany = (new InsuranceCompanyBuilder())->build();

        self::expectException(NameNotSpecifiedException::class);

        $insuranceCompany->changeName('');
    }

    /**
     * @throws EmailConfirmTokenNotSpecifiedException
     * @throws IncorrectConfirmTokenException
     * @throws PasswordNotSpecifiedException
     */
    public function testChangeEmailSuccess(): void
    {
        $insuranceCompany = (new InsuranceCompanyBuilder())->build();

        $insuranceCompany->changeEmail($email = new Email('new@app.test'));

        self::assertEquals($email, $insuranceCompany->getEmail());
    }
}
