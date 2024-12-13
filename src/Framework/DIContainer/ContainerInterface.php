<?php

declare(strict_types=1);

namespace App\Framework\DIContainer;

interface ContainerInterface
{
    /**
     * @template T
     *
     * @param class-string<T>|string $abstract
     * @return T
     */
    public function get(string $abstract): object;

    /**
     * @param class-string|string $abstract
     */
    public function has(string $abstract): bool;

    /**
     * @template T
     *
     * @param class-string<T>|string $abstract
     * @param T $concreteImpl
     */
    public function set(string $abstract, $concreteImpl = null): void;

    /**
     * @param array<string, mixed> $params
     */
    public function callMethod(object $classInstance, string $methodName, array $params = []): mixed;
}
