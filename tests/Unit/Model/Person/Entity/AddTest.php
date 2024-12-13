<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Person\Entity;

use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\NotifierType;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Entity\PhoneNumber;
use App\Tests\Builder\Person\PersonBuilder;
use PHPUnit\Framework\TestCase;

class AddTest extends TestCase
{
    public function testSuccess(): void
    {
        $person = (new PersonBuilder())
            ->withId($id = PersonId::next())
            ->withInsuranceCompanyId($insuranceCompanyId = new InsuranceCompanyId('id'))
            ->withName($name = new Name('Tester', 'Testovka', 'Testovna'))
            ->withEmail($email = new Email('test@app.test'))
            ->withPhoneNumber($phoneNumber = new PhoneNumber('77774001998'))
            ->withNotifierType($notifierType = NotifierType::email())
            ->build();

        self::assertEquals($id, $person->getId());
        self::assertEquals($name, $person->getName());
        self::assertEquals($insuranceCompanyId, $person->getInsuranceCompanyId());
        self::assertEquals($email, $person->getEmail());
        self::assertEquals($phoneNumber, $person->getPhoneNumber());
        self::assertEquals($notifierType, $person->getNotifierType());
    }
}
