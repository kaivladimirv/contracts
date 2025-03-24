<?php

declare(strict_types=1);

namespace App\ReadModel\Person;

use App\Model\Person\Entity\PersonId;
use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Service\Cache\CacheTrait;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Service\CacheItemKeyGenerator;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Etag;

class CachedPersonFetcher implements PersonFetcherInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlPersonFetcher $fetcher,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator
    ) {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAll(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip, Filter $filter): array
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuranceCompanyId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFrom($insuranceCompanyId, __FUNCTION__, $limit, $skip, json_encode($filter));
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $cacheItem->get();
        }

        $data = $this->fetcher->getAll($insuranceCompanyId, $limit, $skip, $filter);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $data);

        return $data;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function isExist(InsuranceCompanyId $insuranceCompanyId, PersonId $id): bool
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuranceCompanyId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFrom($insuranceCompanyId, __FUNCTION__, $id->getValue());
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $cacheItem->get();
        }

        $result = $this->fetcher->isExist($insuranceCompanyId, $id);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $result);

        return $result;
    }
}
