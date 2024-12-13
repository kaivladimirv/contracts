<?php

declare(strict_types=1);

namespace App\Tests\Unit\Model\Contract\Entity\InsuredPerson;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\InsuredPerson\PersonId;
use App\Tests\Builder\Contract\InsuredPersonBuilder;
use PHPUnit\Framework\TestCase;

class AddTest extends TestCase
{
    public function testSuccess(): void
    {
        $insuredPerson = (new InsuredPersonBuilder())
            ->withId($id = new InsuredPersonId('insured_person_id'))
            ->withContractId($contractId = new ContractId('contract_id'))
            ->withPersonId($personId = new PersonId('person_id'))
            ->withPolicyNumber($policyNumber = 'policy_number')
            ->build();

        self::assertEquals($id, $insuredPerson->getId());
        self::assertEquals($contractId, $insuredPerson->getContractId());
        self::assertEquals($personId, $insuredPerson->getPersonId());
        self::assertEquals($policyNumber, $insuredPerson->getPolicyNumber());
        self::assertFalse($insuredPerson->isAllowedToExceedLimit());
    }
}
