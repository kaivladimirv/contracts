<?php

declare(strict_types=1);

namespace App\Tests\Builder\Contract;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\InsuredPerson\PersonId;

class InsuredPersonBuilder
{
    private InsuredPersonId $id;
    private ContractId $contractId;
    private PersonId $personId;
    private string $policyNumber = 'number';
    private bool $isAllowedToExceedLimit = false;

    public function __construct()
    {
        $this->id = InsuredPersonId::next();
        $this->contractId = new ContractId('id');
        $this->personId = new PersonId('id');
    }

    public function withId(InsuredPersonId $id): self
    {
        $clone = clone $this;
        $clone->id = $id;

        return $clone;
    }

    public function withContractId(ContractId $contractId): self
    {
        $clone = clone $this;
        $clone->contractId = $contractId;

        return $clone;
    }

    public function withPersonId(PersonId $personId): self
    {
        $clone = clone $this;
        $clone->personId = $personId;

        return $clone;
    }

    public function withPolicyNumber(string $policyNumber): self
    {
        $clone = clone $this;
        $clone->policyNumber = $policyNumber;

        return $clone;
    }

    public function build(): InsuredPerson
    {
        return new InsuredPerson(
            $this->id,
            $this->contractId,
            $this->personId,
            $this->policyNumber,
            $this->isAllowedToExceedLimit
        );
    }
}
