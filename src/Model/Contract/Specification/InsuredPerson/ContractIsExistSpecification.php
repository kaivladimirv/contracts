<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\InsuredPerson;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\ReadModel\Contract\Contract\ContractFetcherInterface;
use Override;
use App\Model\AbstractSpecification;

class ContractIsExistSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Договор не найден';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly ContractFetcherInterface $contractFetcher,
        private readonly \App\Model\InsuranceCompany\Entity\InsuranceCompanyId $insuranceCompanyId
    ) {
    }

    /**
     * @param InsuredPerson|object $entity
     */
    #[Override]
    public function isSatisfiedBy($entity): bool
    {
        $insuranceCompanyId = new InsuranceCompanyId($this->insuranceCompanyId->getValue());
        $contractId = new ContractId($entity->getContractId()->getValue());

        return $this->contractFetcher->isExist($insuranceCompanyId, $contractId);
    }
}
