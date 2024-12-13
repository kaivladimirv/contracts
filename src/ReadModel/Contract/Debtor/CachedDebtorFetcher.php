<?php

declare(strict_types=1);

namespace App\ReadModel\Contract\Debtor;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Service\Cache\CacheTrait;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Service\Balance\CacheItemKeyGenerator;
use App\ReadModel\Contract\Debtor\Dto\DebtorDtoCollection;
use App\ReadModel\Contract\Debtor\Dto\DebtorDtoConvertor;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Etag;

class CachedDebtorFetcher implements DebtorFetcherInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlDebtorFetcher $fetcher,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly DebtorDtoConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(ContractId $contractId): DebtorDtoCollection
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdateByContract($contractId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFromContractId($contractId);
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $this->convertor->convertToCollection($cacheItem->get());
        }

        $debtors = $this->fetcher->get($contractId);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $debtors->toArray());

        return $debtors;
    }
}
