<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\InsuredPerson;

use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\PersonId;
use App\ReadModel\Person\PersonFetcherInterface;
use Override;
use App\Model\AbstractSpecification;

class PersonIsExistSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Персона не найдена в справочнике персон';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PersonFetcherInterface $personFetcher,
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
        $personId = new PersonId($entity->getPersonId()->getValue());

        return $this->personFetcher->isExist($insuranceCompanyId, $personId);
    }
}
