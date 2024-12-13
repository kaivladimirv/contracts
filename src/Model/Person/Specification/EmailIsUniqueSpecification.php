<?php

declare(strict_types=1);

namespace App\Model\Person\Specification;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Person\Entity\Person;
use App\Model\Person\Repository\PersonRepositoryInterface;

class EmailIsUniqueSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Персона с указанным электронным адресом уже существует';

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

        if (!$entity->getEmail()) {
            return true;
        }

        $found = $this->personRepository->findByEmail($entity->getInsuranceCompanyId(), $entity->getEmail());

        if ($found and !$found->getId()->isEqual($entity->getId())) {
            return false;
        }

        return true;
    }
}
