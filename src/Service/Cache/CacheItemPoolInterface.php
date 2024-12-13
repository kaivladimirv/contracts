<?php

declare(strict_types=1);

namespace App\Service\Cache;

interface CacheItemPoolInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getItem(string $key): CacheItemInterface;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     *
     * @return CacheItemInterface[]
     */
    public function getItems(array $keys = []): array;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function hasItem(string $key): bool;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function clear(): void;

    /**
     * @psalm-api
     */
    public function deleteItem(string $key): bool;

    /**
     * @psalm-api
     */
    public function deleteItems(array $keys): bool;

    /**
     * @psalm-api
     */
    public function save(CacheItemInterface $item): bool;
}
