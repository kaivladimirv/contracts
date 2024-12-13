<?php

declare(strict_types=1);

namespace App\Service\Cache;

use Override;
use App\Service\Cache\Exception\InvalidArgumentException;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Exception;

class CacheItem implements CacheItemInterface
{
    private const string DEFAULT_EXPIRATION = 'now +30 days';
    private ?Etag $etag = null;
    private mixed $value = false;
    private ?DateTimeInterface $expiration;

    public function __construct(private readonly string $key)
    {
        $this->expiration = new DateTime(self::DEFAULT_EXPIRATION);
    }

    #[Override]
    public function getKey(): string
    {
        return $this->key;
    }

    #[Override]
    public function get(): mixed
    {
        return $this->value;
    }

    #[Override]
    public function getTtlInSecond(): int
    {
        return $this->expiration ? (int)$this->expiration->format('U') - time() : 0;
    }

    #[Override]
    public function getEtag(): ?Etag
    {
        return $this->etag;
    }

    #[Override]
    public function hasEtag(): bool
    {
        return !is_null($this->etag);
    }

    #[Override]
    public function isHit(): bool
    {
        return ($this->value !== false);
    }

    #[Override]
    public function set(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    #[Override]
    public function setEtag(Etag $etag): self
    {
        $this->etag = $etag;

        return $this;
    }

    /**
     * Устанавливает дату истечения срока действия
     */
    #[Override]
    public function expiresAt(?DateTimeInterface $expiration): self
    {
        $this->expiration = $expiration;

        return $this;
    }

    /**
     * @psalm-api
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    #[Override]
    public function expiresAfter(DateInterval|int|null $time): self
    {
        if (is_int($time)) {
            $this->expiration = new DateTime();
            $this->expiration->add(new DateInterval('PT' . $time . 'S'));

            return $this;
        }

        if ($time instanceof DateInterval) {
            $this->expiration = new DateTime();
            $this->expiration->add($time);

            return $this;
        }

        if ($time === null) {
            $this->expiration = null;

            return $this;
        }

        throw new InvalidArgumentException('Invalid parameter type');
    }

    #[Override]
    public function isEtagEqual(Etag $otherEtag): bool
    {
        return $this->etag and $this->etag->isEqual($otherEtag);
    }
}
