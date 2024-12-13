<?php

declare(strict_types=1);

namespace App\Service\Cache;

use DateInterval;
use DateTimeInterface;

interface CacheItemInterface
{
    public function getKey(): string;

    public function get(): mixed;

    public function getTtlInSecond(): int;

    public function getEtag(): ?Etag;

    public function hasEtag(): bool;

    public function isHit(): bool;

    /**
     * @psalm-api
     */
    public function set(mixed $value): self;

    /**
     * @psalm-api
     */
    public function setEtag(Etag $etag): self;

    /**
     * @psalm-api
     */
    public function expiresAt(?DateTimeInterface $expiration): self;

    /**
     * @psalm-api
     */
    public function expiresAfter(DateInterval|int|null $time): self;

    public function isEtagEqual(Etag $otherEtag): bool;
}
