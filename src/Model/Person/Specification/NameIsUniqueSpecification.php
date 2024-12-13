<?php

declare(strict_types=1);

namespace App\Model\Person\Specification;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Person\Entity\Person;
use App\Model\Person\Repository\PersonRepositoryInterface;

class NameIsUniqueSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Персона с указанным именем уже существует';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly PersonRepositoryInterface $personRepository)
    {
    }

    #[Override]
    public function isSatisfiedBy(object $entity): bool
    {
        /** @var Person $entity */

        $found = $this->personRepository->findByName($entity->getInsuranceCompanyId(), $entity->getName());

        if ($found and !$found->getId()->isEqual($entity->getId())) {
            return false;
        }

        return true;
    }
}
