<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\InsuranceCompany\Entity;

use App\Model\InsuranceCompany\Entity\AccessToken;
use App\Model\InsuranceCompany\Exception\AccessTokenIsExpiredException;
use App\Model\InsuranceCompany\Exception\IncorrectConfirmTokenException;
use App\Tests\Builder\InsuranceCompany\InsuranceCompanyBuilder;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ChangeAccessTokenTest extends TestCase
{
    /**
     * @throws IncorrectConfirmTokenException
     * @throws AccessTokenIsExpiredException
     */
    public function testSuccess(): void
    {
        $insuranceCompany = (new InsuranceCompanyBuilder())->confirmed()->build();

        $now = new DateTimeImmutable();
        $accessToken = new AccessToken('token', $now->modify('+1 day'));

        $insuranceCompany->changeAccessToken($accessToken, new DateTimeImmutable());

        self::assertEquals($accessToken->getToken(), $insuranceCompany->getAccessToken()->getToken());
        self::assertEquals($accessToken->getExpires(), $insuranceCompany->getAccessToken()->getExpires());
    }

    /**
     * @throws IncorrectConfirmTokenException
     * @throws AccessTokenIsExpiredException
     */
    public function testFail(): void
    {
        $insuranceCompany = (new InsuranceCompanyBuilder())->confirmed()->build();

        $now = new DateTimeImmutable();
        $accessToken = new AccessToken('token', $now->modify('-1 day'));

        $this->expectException(AccessTokenIsExpiredException::class);
        $insuranceCompany->changeAccessToken($accessToken, new DateTimeImmutable());
    }
}
