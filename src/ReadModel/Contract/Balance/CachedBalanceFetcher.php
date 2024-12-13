<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Balance;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Service\Cache\CacheTrait;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Service\Balance\CacheItemKeyGenerator;
use App\ReadModel\Contract\Balance\Dto\BalanceDtoCollection;
use App\ReadModel\Contract\Balance\Dto\BalanceDtoConvertor;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Etag;

class CachedBalanceFetcher implements BalanceFetcherInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlBalanceFetcher $fetcher,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly BalanceDtoConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAllByInsuredPersonId(InsuredPersonId $insuredPersonId): BalanceDtoCollection
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPersonId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFrom($insuredPersonId, __FUNCTION__);
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $this->convertor->convertToCollection($cacheItem->get());
        }

        $balances = $this->fetcher->getAllByInsuredPersonId($insuredPersonId);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $balances->toArray());

        return $balances;
    }
}
