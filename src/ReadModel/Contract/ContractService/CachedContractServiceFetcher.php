<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\ContractService;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Service\Cache\CacheTrait;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Service\ContractService\CacheItemKeyGenerator;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Etag;

class CachedContractServiceFetcher implements ContractServiceFetcherInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlContractServiceFetcher $fetcher,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator
    ) {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAll(ContractId $contractId, int $limit, int $skip, Filter $filter): array
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($contractId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFrom($contractId, __FUNCTION__, $limit, $skip, json_encode($filter));
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $cacheItem->get();
        }

        $data = $this->fetcher->getAll($contractId, $limit, $skip, $filter);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $data);

        return $data;
    }
}
