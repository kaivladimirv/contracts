<?php

declare(strict_types=1);

namespace App\Service\Cache;

trait CacheTrait
{
    private readonly CacheItemPoolInterface $cache;

    private function saveToCache(CacheItemInterface $cacheItem, $newValue): void
    {
        if (!empty($newValue)) {
            $cacheItem->set($newValue);
            $this->cache->save($cacheItem);
        }
    }
}
