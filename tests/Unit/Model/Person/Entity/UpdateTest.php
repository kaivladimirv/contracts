<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Person\Entity;

use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\NotifierType;
use App\Model\Person\Entity\PhoneNumber;
use App\Tests\Builder\Person\PersonBuilder;
use DomainException;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    public function testSuccess(): void
    {
        $person = (new PersonBuilder())->build();

        $person->changeName($newName = new Name('Tester', 'Testovka', 'Testovna'));
        self::assertEquals($newName, $person->getName());

        $person->changeEmail($newEmail = new Email('test@app.test'));
        self::assertEquals($newEmail, $person->getEmail());

        $person->changePhoneNumber($newPhoneNumber = new PhoneNumber('77774001998'));
        self::assertEquals($newPhoneNumber, $person->getPhoneNumber());

        $person->changeNotifierType($newNotifierType = NotifierType::telegram());
        self::assertEquals($newNotifierType, $person->getNotifierType());

        self::assertTrue($person->shouldBeNotified());

        $person->deleteEmail();
        self::assertNull($person->getEmail());

        $person->deletePhoneNumber();
        self::assertNull($person->getPhoneNumber());

        $person->deleteNotifierType();
        self::assertFalse($person->shouldBeNotified());
    }

    public function testEmailNotSpecifiedForNotification(): void
    {
        $person = (new PersonBuilder())
            ->withEmail(null)
            ->build();

        $this->expectException(DomainException::class);

        $person->changeNotifierType(NotifierType::email());
    }

    public function testPhoneNumberNotSpecifiedForNotification(): void
    {
        $person = (new PersonBuilder())
            ->withPhoneNumber(null)
            ->build();

        $this->expectException(DomainException::class);

        $person->changeNotifierType(NotifierType::telegram());
    }
}
