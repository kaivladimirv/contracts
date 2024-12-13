<?php

declare(strict_types=1);

namespace App\Model;

interface SpecificationInterface
{
    public function isSatisfiedBy(object $entity): bool;

    public function getReasonNotSatisfied(): string;

    public function throwExceptionIfIsNotSatisfiedBy(object $entity);
}
