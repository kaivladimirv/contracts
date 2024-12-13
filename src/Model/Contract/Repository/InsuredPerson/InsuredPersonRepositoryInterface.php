<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\InsuredPerson;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonCollection;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\InsuredPerson\PersonId;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;

interface InsuredPersonRepositoryInterface
{
    public function get(ContractId $contractId, int $limit, int $skip): InsuredPersonCollection;

    /**
     * @throws InsuredPersonNotFoundException
     */
    public function getOne(InsuredPersonId $insuredPersonId): InsuredPerson;

    public function add(InsuredPerson $insuredPerson): void;

    public function update(InsuredPerson $insuredPerson): void;

    public function delete(InsuredPerson $insuredPerson): void;

    public function findByPolicyNumber(ContractId $contractId, string $policyNumber): ?InsuredPerson;

    public function findByPersonId(ContractId $contractId, PersonId $personId): ?InsuredPerson;
}
