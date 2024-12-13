<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\InsuredPerson;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\Model\Contract\Repository\InsuredPerson\InsuredPersonRepositoryInterface;

class PolicyNumberIsUniqueSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Указанный номер полиса уже присвоен другому застрахованному лицу';

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

        $found = $this->insuredPersonRepository->findByPolicyNumber($entity->getContractId(), $entity->getPolicyNumber());

        if ($found and !$found->getId()->isEqual($entity->getId())) {
            return false;
        }

        return true;
    }
}
