<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\InsuranceCompany\Entity;

use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Exception\IncorrectConfirmTokenException;
use PHPUnit\Framework\TestCase;

class ConfirmTest extends TestCase
{
    /**
     * @throws IncorrectConfirmTokenException
     */
    public function testSuccess(): void
    {
        $insuranceCompany = InsuranceCompany::register(
            InsuranceCompanyId::next(),
            'Company test',
            new Email('company@app.test'),
            'hash',
            $emailConfirmToken = 'token'
        );

        $insuranceCompany->confirmRegister($emailConfirmToken);

        self::assertEmpty($insuranceCompany->getEmailConfirmToken());
        self::assertTrue($insuranceCompany->isEmailConfirmed());
    }

    public function testFail(): void
    {
        $insuranceCompany = InsuranceCompany::register(
            InsuranceCompanyId::next(),
            'Company test',
            new Email('company@app.test'),
            'hash',
            'token'
        );

        $this->expectException(IncorrectConfirmTokenException::class);
        $insuranceCompany->confirmRegister('incorrect-token');
    }
}
