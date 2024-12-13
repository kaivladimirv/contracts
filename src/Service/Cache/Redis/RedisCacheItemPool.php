<?php

declare(strict_types=1);

namespace App\Service\Cache\Redis;

use Override;
use App\Service\Cache\CacheItemBuilder;
use App\Service\Cache\CacheItemInterface;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Etag;
use App\Service\Cache\Exception\CacheException;
use App\Service\Cache\Exception\InvalidArgumentException;
use Redis;
use RedisException;

readonly class RedisCacheItemPool implements CacheItemPoolInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private Redis $redis, private CacheItemBuilder $itemBuilder)
    {
    }

    /**
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    #[Override]
    public function getItem(string $key): CacheItemInterface
    {
        try {
            $value = $this->redis->get($key);
            $ttl = $this->redis->ttl($key);

            return $this->buildItem($key, $value, $ttl);
        } catch (RedisException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * @throws CacheException
     * @throws InvalidArgumentException
     */
    #[Override]
    public function getItems(array $keys = []): array
    {
        try {
            $values = $this->redis->mGet($keys);
            $values = array_combine($keys, $values);

            $items = [];
            foreach ($values as $key => $value) {
                $ttl = $this->redis->ttl($key);

                $items[$key] = $this->buildItem($key, $value, $ttl);
            }

            return $items;
        } catch (RedisException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function buildItem(string $key, $value, $ttl): CacheItemInterface
    {
        $value = $this->valueDecode($value);
        $ttl = ($ttl !== false ? $ttl : null);

        $etag = $this->extractEtagFrom($value);
        $value = $this->extractDataFrom($value);

        return $this->itemBuilder
            ->setKey($key)
            ->setEtag($etag)
            ->setValue($value)
            ->setTtl($ttl)
            ->build();
    }

    private function valueDecode(mixed $value): mixed
    {
        if ($value === false) {
            return false;
        }

        $valueAsArray = json_decode((string) $value, true);

        return json_last_error() === JSON_ERROR_NONE ? $valueAsArray : $value;
    }

    private function extractEtagFrom(mixed $value): ?Etag
    {
        return ($this->hasEtag($value) ? new Etag($value['etag']) : null);
    }

    private function extractDataFrom(mixed $value): mixed
    {
        return ($this->hasEtag($value) ? $value['data'] : $value);
    }

    private function hasEtag($value): bool
    {
        return is_array($value) and array_key_exists('etag', $value);
    }

    /**
     * @throws CacheException
     */
    #[Override]
    public function hasItem(string $key): bool
    {
        try {
            return ($this->redis->get($key) !== false);
        } catch (RedisException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * @throws CacheException
     */
    #[Override]
    public function clear(): void
    {
        try {
            $this->redis->flushDB();
        } catch (RedisException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * @throws CacheException
     */
    #[Override]
    public function deleteItem(string $key): bool
    {
        try {
            $this->redis->del($key);

            return true;
        } catch (RedisException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * @throws CacheException
     */
    #[Override]
    public function deleteItems(array $keys): bool
    {
        try {
            $this->redis->del($keys);

            return true;
        } catch (RedisException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * @throws CacheException
     */
    #[Override]
    public function save(CacheItemInterface $item): bool
    {
        try {
            if ($item->hasEtag()) {
                $value = [
                    'data' => $item->get(),
                    'etag' => $item->getEtag()->getValue(),
                ];
            } else {
                $value = $item->get();
            }

            if ($item->getTtlInSecond() !== 0) {
                return $this->redis->set($item->getKey(), json_encode($value), $item->getTtlInSecond()) === true;
            } else {
                return $this->redis->set($item->getKey(), json_encode($value)) === true;
            }
        } catch (RedisException $e) {
            throw new CacheException($e->getMessage());
        }
    }
}
