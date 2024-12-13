<?php

declare(strict_types=1);

namespace App\Model\Service\Repository;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Model\Service\Exception\ServiceIsNotDeletedException;
use App\Service\Cache\CacheTrait;
use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\Service;
use App\Model\Service\Entity\ServiceCollection;
use App\Model\Service\Entity\ServiceId;
use App\Model\Service\Exception\ServiceNotFoundException;
use App\Model\Service\Service\CacheItemKeyGenerator;
use App\Model\Service\Service\ServiceConvertor;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Etag;

class CachedServiceRepository implements ServiceRepositoryInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlServiceRepository $repository,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly ServiceConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip): ServiceCollection
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuranceCompanyId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFrom($insuranceCompanyId, __FUNCTION__, $limit, $skip));

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $this->convertor->convertToCollection($cacheItem->get());
        }

        $services = $this->repository->get($insuranceCompanyId, $limit, $skip);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $services->toArray());

        return $services;
    }

    /**
     * @throws QueryBuilderException
     * @throws ServiceNotFoundException
     */
    #[Override]
    public function getOne(ServiceId $id): Service
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromId($id));

        if ($cacheItem->isHit()) {
            return $this->convertor->convertToEntity($cacheItem->get());
        }

        $service = $this->repository->getOne($id);

        $this->saveToCache($cacheItem, $service->toArray());

        return $service;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(Service $service): void
    {
        $this->repository->add($service);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($service->getInsuranceCompanyId()));
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(Service $service): void
    {
        $this->repository->update($service);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($service->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($service->getInsuranceCompanyId()));
    }

    /**
     * @throws QueryBuilderException
     * @throws ServiceIsNotDeletedException
     */
    #[Override]
    public function delete(Service $service): void
    {
        $this->repository->delete($service);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($service->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($service->getInsuranceCompanyId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromName($service->getInsuranceCompanyId(), $service->getName()));
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByName(InsuranceCompanyId $insuranceCompanyId, string $name): ?Service
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromName($insuranceCompanyId, $name));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }

        $service = $this->repository->findByName($insuranceCompanyId, $name);

        $this->saveToCache($cacheItem, $service?->toArray());

        return $service;
    }
}
