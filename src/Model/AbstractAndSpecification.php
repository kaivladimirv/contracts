<?php

declare(strict_types=1);

namespace App\Model;

use Override;
use App\Framework\DIContainer\ContainerInterface;

abstract class AbstractAndSpecification extends AbstractSpecification
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    #[Override]
    final public function isSatisfiedBy(object $entity): bool
    {
        foreach ($this->getSpecificationClassNames() as $specificationClass) {

            /** @var SpecificationInterface $specification */
            $specification = $this->container->get($specificationClass);

            if (!$specification->isSatisfiedBy($entity)) {
                $this->reasonNotSatisfied = $specification->getReasonNotSatisfied();

                return false;
            }
        }

        return true;
    }

    abstract protected function getSpecificationClassNames(): array;
}