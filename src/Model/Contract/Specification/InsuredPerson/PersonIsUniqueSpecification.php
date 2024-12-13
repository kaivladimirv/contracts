<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\InsuredPerson;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\Model\Contract\Repository\InsuredPerson\InsuredPersonRepositoryInterface;

class PersonIsUniqueSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Указанная персона уже добавлена в договор';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly InsuredPersonRepositoryInterface $insuredPersonRepository)
    {
    }

    #[Override]
    public function isSatisfiedBy($entity): bool
    {
        /** @var InsuredPerson $entity */

        $found = $this->insuredPersonRepository->findByPersonId($entity->getContractId(), $entity->getPersonId());

        if ($found and !$found->getId()->isEqual($entity->getId())) {
            return false;
        }

        return true;
    }
}
