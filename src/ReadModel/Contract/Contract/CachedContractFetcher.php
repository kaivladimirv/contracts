<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Contract;

use App\Model\Contract\Entity\Contract\ContractId;
use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Service\Cache\CacheTrait;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\Contract\Service\Contract\CacheItemKeyGenerator;
use App\ReadModel\Contract\Contract\Filter\Filter;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Etag;

class CachedContractFetcher implements ContractFetcherInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlContractFetcher $fetcher,
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
    public function isExist(InsuranceCompanyId $insuranceCompanyId, ContractId $contractId): bool
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuranceCompanyId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFrom($insuranceCompanyId, __FUNCTION__, $contractId->getValue());
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $cacheItem->get();
        }

        $data = $this->fetcher->isExist($insuranceCompanyId, $contractId);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $data);

        return $data;
    }
}
