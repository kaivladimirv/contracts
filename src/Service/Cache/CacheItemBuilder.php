<?php

declare(strict_types=1);

namespace App\Service\Cache;

use App\Service\Cache\Exception\InvalidArgumentException;
use DateInterval;
use DateTimeInterface;
use Exception;

class CacheItemBuilder
{
    private string $key;
    private mixed $value;
    private int|null|DateTimeInterface|DateInterval $ttl = null;
    private ?Etag $etag = null;

    public function setKey(string $key): self
    {
        $clone = clone $this;
        $clone->key = $key;

        return $clone;
    }

    public function setEtag(?Etag $etag): self
    {
        $clone = clone $this;
        $clone->etag = $etag;

        return $clone;
    }

    public function setValue(mixed $value): self
    {
        $clone = clone $this;
        $clone->value = $value;

        return $clone;
    }

    public function setTtl(DateInterval|DateTimeInterface|int|null $ttl): self
    {
        $clone = clone $this;
        $this->ttl = $ttl;

        return $clone;
    }

    /**
     * @throws InvalidArgumentException|Exception
     */
    public function build(): CacheItemInterface
    {
        $cacheItem = new CacheItem($this->key);

        if ($this->value !== false) {
            $cacheItem->set($this->value);
        }

        $cacheItem->expiresAfter($this->ttl);

        if ($this->etag) {
            $cacheItem->setEtag($this->etag);
        }

        return $cacheItem;
    }
}
