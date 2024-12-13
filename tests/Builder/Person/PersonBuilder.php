<?php

declare(strict_types=1);

namespace App\Tests\Builder\Person;

use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\NotifierType;
use App\Model\Person\Entity\Person;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Entity\PhoneNumber;

class PersonBuilder
{
    private PersonId $id;
    private Name $name;
    private InsuranceCompanyId $insuranceCompanyId;
    private ?Email $email;
    private ?PhoneNumber $phoneNumber;
    private ?NotifierType $notifierType = null;

    public function __construct()
    {
        $this->id = PersonId::next();
        $this->name = new Name('Tester', 'Test', 'Testov');
        $this->insuranceCompanyId = new InsuranceCompanyId('id');
        $this->email = new Email('tester@test.ts');
        $this->phoneNumber = new PhoneNumber('77770000000');
    }

    public function withId(PersonId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    public function withInsuranceCompanyId(InsuranceCompanyId $insuranceCompanyId): self
    {
        $clone = clone $this;
        $clone->insuranceCompanyId = $insuranceCompanyId;

        return $clone;
    }

    public function withName(Name $name): self
    {
        $clone = clone $this;
        $clone->name = $name;

        return $clone;
    }

    public function withEmail(?Email $email): self
    {
        $clone = clone $this;
        $clone->email = $email;

        return $clone;
    }

    public function withPhoneNumber(?PhoneNumber $phoneNumber): self
    {
        $clone = clone $this;
        $clone->phoneNumber = $phoneNumber;

        return $clone;
    }

    public function withNotifierType(NotifierType $notifierType): self
    {
        $clone = clone $this;
        $clone->notifierType = $notifierType;

        return $clone;
    }

    public function build(): Person
    {
        return Person::create(
            $this->id,
            $this->name,
            $this->insuranceCompanyId,
            $this->email,
            $this->phoneNumber,
            $this->notifierType
        );
    }
}
