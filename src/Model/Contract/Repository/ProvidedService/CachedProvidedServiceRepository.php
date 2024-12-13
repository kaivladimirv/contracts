<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\ProvidedService;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\ProvidedService\Id;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Entity\ProvidedService\ProvidedServiceCollection;
use App\Model\Contract\Exception\ProvidedService\ProvidedServiceNotFoundException;
use App\Model\Contract\Service\ProvidedService\CacheItemKeyGenerator;
use App\Model\Contract\Service\ProvidedService\ProvidedServiceConvertor;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\CacheTrait;
use App\Service\Cache\Etag;

class CachedProvidedServiceRepository implements ProvidedServiceRepositoryInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlProvidedServiceRepository $repository,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly ProvidedServiceConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(InsuredPersonId $insuredPersonId, int $limit, int $skip): ProvidedServiceCollection
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPersonId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFrom($insuredPersonId, __FUNCTION__, $limit, $skip));

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $this->convertor->convertToCollection($cacheItem->get());
        }

        $providedServices = $this->repository->get($insuredPersonId, $limit, $skip);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $providedServices->toArray());

        return $providedServices;
    }

    /**
     * @throws QueryBuilderException
     * @throws ProvidedServiceNotFoundException
     */
    #[Override]
    public function getOne(Id $id): ProvidedService
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromId($id));

        if ($cacheItem->isHit()) {
            return $this->convertor->convertToEntity($cacheItem->get());
        }

        $providedService = $this->repository->getOne($id);

        $this->saveToCache($cacheItem, $providedService->toArray());

        return $providedService;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(ProvidedService $providedService): void
    {
        $this->repository->add($providedService);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($providedService->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($providedService->getInsuredPersonId()));
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(ProvidedService $providedService): void
    {
        $this->repository->update($providedService);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($providedService->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($providedService->getInsuredPersonId()));
    }
}
