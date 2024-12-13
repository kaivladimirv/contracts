<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\Contract;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Contract\Entity\Contract\Contract;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;

class ContractNumberIsUniqueSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Договор с указанным номер уже существует';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly ContractRepositoryInterface $contractRepository)
    {
    }

    #[Override]
    public function isSatisfiedBy($entity): bool
    {
        /** @var Contract $entity */

        $found = $this->contractRepository->findByNumber($entity->getInsuranceCompanyId(), $entity->getNumber());

        if ($found and !$found->getId()->isEqual($entity->getId())) {
            return false;
        }

        return true;
    }
}
