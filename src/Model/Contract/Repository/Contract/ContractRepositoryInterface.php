<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\Contract;

use App\Model\Contract\Entity\Contract\Contract;
use App\Model\Contract\Entity\Contract\ContractCollection;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;

interface ContractRepositoryInterface
{
    public function get(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip): ContractCollection;

    /**
     * @throws ContractNotFoundException
     */
    public function getOne(ContractId $id): Contract;

    public function add(Contract $contract): void;

    public function update(Contract $contract): void;

    public function delete(Contract $contract): void;

    public function findByNumber(InsuranceCompanyId $insuranceCompanyId, string $number): ?Contract;
}
