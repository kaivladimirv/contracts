<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\InsuranceCompany\Entity;

use App\Model\InsuranceCompany\Exception\EmailConfirmTokenNotSpecifiedException;
use App\Model\InsuranceCompany\Exception\IncorrectConfirmTokenException;
use App\Model\InsuranceCompany\Exception\PasswordNotSpecifiedException;
use App\Tests\Builder\InsuranceCompany\InsuranceCompanyBuilder;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    /**
     * @throws IncorrectConfirmTokenException
     * @throws EmailConfirmTokenNotSpecifiedException
     * @throws PasswordNotSpecifiedException
     */
    public function testSuccess(): void
    {
        $insuranceCompany = (new InsuranceCompanyBuilder())->build();

        $insuranceCompany->delete();

        self::assertTrue($insuranceCompany->isDeleted());
    }
}
