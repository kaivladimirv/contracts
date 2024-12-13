<?php

declare(strict_types=1);

namespace App\Service\Hydrator;

use Override;
use DateTimeImmutable;
use Exception;
use ReflectionException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use UnexpectedValueException;

class Hydrator implements HydratorInterface
{
    private array $reflectionClassMap = [];

    /**
     * @template T of object
     *
     * @param class-string<T>|T $entityOrEntityClassName
     * @return T|object
     * @throws ReflectionException
     */
    #[Override]
    public function hydrate($entityOrEntityClassName, array $data): object
    {
        if (is_object($entityOrEntityClassName)) {
            $entity = clone $entityOrEntityClassName;
        } else {
            $reflectionClass = $this->getReflectionClass($entityOrEntityClassName);
            $entity = $reflectionClass->newInstanceWithoutConstructor();
        }

        foreach ($data as $fieldName => $value) {
            $propertyName = $fieldName;

            $entity = $this->hydrateProperty($entity, $propertyName, $value);
        }

        return $entity;
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    #[Override]
    public function hydrateProperty(object $targetEntity, string $propertyName, mixed $value): object
    {
        $entity = clone $targetEntity;

        $reflectionClass = $this->getReflectionClass($entity::class);

        $property = $this->getPropertyFromReflectionClass($reflectionClass, $propertyName);

        $property->setValue($entity, $this->performTypeConversions($value, $property->getType()));

        return $entity;
    }

    /**
     * @throws Exception
     */
    private function performTypeConversions(mixed $value, ReflectionNamedType $type): mixed
    {
        return match ($type->getName()) {
            'DateTimeImmutable' => is_null($value) ? $value : $this->convertToDateTime($value),
            'bool' => $this->convertToBool($value),
            'int' => $this->convertToIntOrNull($value),
            default => $value,
        };
    }

    /**
     * @throws Exception
     */
    private function convertToDateTime($value): DateTimeImmutable
    {
        return $value instanceof DateTimeImmutable ? $value : new DateTimeImmutable($value);
    }

    private function convertToBool($value): bool
    {
        $result = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if (($result === null) or (trim((string) $value) === '')) {
            return $value;
        }

        return $result;
    }

    private function convertToIntOrNull($value): ?int
    {
        if (is_null($value) or ($value === '')) {
            return null;
        }

        $result = filter_var($value, FILTER_VALIDATE_INT);

        if ($result === false) {
            throw new UnexpectedValueException('The value is not a valid integer.');
        }

        return $result;
    }

    /**
     * @throws ReflectionException
     */
    private function getReflectionClass(string $className): ReflectionClass
    {
        return $this->reflectionClassMap[$className] ?? ($this->reflectionClassMap[$className] = new ReflectionClass($className));
    }

    /**
     * @throws ReflectionException
     */
    private function getPropertyFromReflectionClass(
        ReflectionClass $reflectionClass,
        string $propertyName
    ): ReflectionProperty {
        return $reflectionClass->getProperty($propertyName);
    }
}
