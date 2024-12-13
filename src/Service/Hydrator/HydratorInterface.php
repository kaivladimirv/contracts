<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

interface HydratorInterface
{
    /**
     * @template T of object
     *
     * @param class-string<T>|T $entityOrEntityClassName
     * @return T|object
     */
    public function hydrate($entityOrEntityClassName, array $data): object;

    public function hydrateProperty(object $targetEntity, string $propertyName, mixed $value): object;
}
