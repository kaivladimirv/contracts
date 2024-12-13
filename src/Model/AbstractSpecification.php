<?php

declare(strict_types=1);

namespace App\Model;

use Override;
use DomainException;

abstract class AbstractSpecification implements SpecificationInterface
{
    protected string $reasonNotSatisfied = '';

    #[Override]
    final public function throwExceptionIfIsNotSatisfiedBy(object $entity): void
    {
        if (!$this->isSatisfiedBy($entity)) {
            throw new DomainException($this->getReasonNotSatisfied());
        }
    }

    #[Override]
    final public function getReasonNotSatisfied(): string
    {
        return $this->reasonNotSatisfied;
    }
}
