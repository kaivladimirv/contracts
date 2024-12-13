<?php

declare(strict_types=1);

namespace App\Model\Service\Specification;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Service\Entity\Service;
use App\Model\Service\Repository\ServiceRepositoryInterface;

class NameIsUniqueSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Услуга с указанным названием уже существует';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly ServiceRepositoryInterface $serviceRepository)
    {
    }

    #[Override]
    public function isSatisfiedBy(object $entity): bool
    {
        /** @var Service $entity */

        $found = $this->serviceRepository->findByName($entity->getInsuranceCompanyId(), $entity->getName());

        if ($found and !$found->getId()->isEqual($entity->getId())) {
            return false;
        }

        return true;
    }
}
