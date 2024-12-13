<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Specification;

use Override;
use App\Model\AbstractSpecification;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;

class NameIsUniqueSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Страховая компания с указанным названием уже зарегистрирована';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly InsuranceCompanyRepositoryInterface $insuranceCompanyRepository)
    {
    }

    #[Override]
    public function isSatisfiedBy(object $entity): bool
    {
        /** @var InsuranceCompany $entity */

        $found = $this->insuranceCompanyRepository->findOneByName($entity->getName());

        if ($found and !$found->getId()->isEqual($entity->getId())) {
            return false;
        }

        return true;
    }
}
