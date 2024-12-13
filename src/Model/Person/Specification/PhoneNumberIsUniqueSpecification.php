<?php

declare(strict_types=1);

namespace App\Model\Person\Specification;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Person\Entity\Person;
use App\Model\Person\Repository\PersonRepositoryInterface;

class PhoneNumberIsUniqueSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Персона с указанным номером телефона уже существует';

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

        if (!$entity->getPhoneNumber()) {
            return true;
        }

        $found = $this->personRepository->findByPhoneNumber(
            $entity->getInsuranceCompanyId(),
            $entity->getPhoneNumber()
        );

        if ($found and !$found->getId()->isEqual($entity->getId())) {
            return false;
        }

        return true;
    }
}
