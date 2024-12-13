<?php

declare(strict_types=1);

namespace App\ReadModel\ProvidedService;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Service\Cache\CacheTrait;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Service\ProvidedService\CacheItemKeyGenerator;
use App\ReadModel\ProvidedService\Dto\ExpenseDto;
use App\ReadModel\ProvidedService\Dto\ExpenseDtoConvertor;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Etag;

class CachedProvidedServiceFetcher implements ProvidedServiceFetcherInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlProvidedServiceFetcher $fetcher,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly ExpenseDtoConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAllByInsuredPerson(
        InsuredPersonId $insuredPersonId,
        int $limit,
        int $skip,
        Filter $filter
    ): array {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPersonId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFrom($insuredPersonId, __FUNCTION__, $limit, $skip, json_encode($filter));
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $cacheItem->get();
        }

        $data = $this->fetcher->getAllByInsuredPerson($insuredPersonId, $limit, $skip, $filter);

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
    public function getQuantityByService(InsuredPersonId $insuredPersonId, ServiceId $serviceId, int $limitType): float
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPersonId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFrom($insuredPersonId, __FUNCTION__, $serviceId->getValue(), $limitType);
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $cacheItem->get();
        }

        $data = $this->fetcher->getQuantityByService($insuredPersonId, $serviceId, $limitType);

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
    public function getAmountByService(InsuredPersonId $insuredPersonId, ServiceId $serviceId, int $limitType): float
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPersonId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFrom($insuredPersonId, __FUNCTION__, $serviceId->getValue(), $limitType);
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $cacheItem->get();
        }

        $data = $this->fetcher->getAmountByService($insuredPersonId, $serviceId, $limitType);

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
    public function getAmountByInsuredPerson(InsuredPersonId $insuredPersonId): float
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPersonId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFrom($insuredPersonId, __FUNCTION__);
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $cacheItem->get();
        }

        $data = $this->fetcher->getAmountByInsuredPerson($insuredPersonId);

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
    public function getExpenseByService(InsuredPersonId $insuredPersonId, ServiceId $serviceId, LimitType $limitType): ExpenseDto
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPersonId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFrom($insuredPersonId, __FUNCTION__, $serviceId->getValue(), $limitType->getValue()));

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $this->convertor->convertToDto($cacheItem->get());
        }

        $expense = $this->fetcher->getExpenseByService($insuredPersonId, $serviceId, $limitType);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $expense->toArray());

        return $expense;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function existsForInsuredPerson(InsuredPersonId $insuredPersonId): bool
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPersonId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $key = $this->cacheItemKeyGenerator->generateFrom($insuredPersonId, __FUNCTION__);
        $cacheItem = $this->cache->getItem($key);

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $cacheItem->get();
        }

        $result = $this->fetcher->existsForInsuredPerson($insuredPersonId);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $result);

        return $result;
    }
}
