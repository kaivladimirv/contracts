<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\ContractService;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ContractService;
use App\Model\Contract\Entity\ContractService\ContractServiceCollection;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Exception\ContractService\ContractServiceIsNotDeletedException;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;
use App\Model\Contract\Service\ContractService\CacheItemKeyGenerator;
use App\Model\Contract\Service\ContractService\ContractServiceConvertor;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\CacheTrait;
use App\Service\Cache\Etag;

class CachedContractServiceRepository implements ContractServiceRepositoryInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlContractServiceRepository $repository,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly ContractServiceConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function getAll(ContractId $contractId): ContractServiceCollection
    {
        return $this->get($contractId, 0, 0);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(ContractId $contractId, int $limit, int $skip): ContractServiceCollection
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($contractId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFrom($contractId, __FUNCTION__, $limit, $skip));

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $this->convertor->convertToCollection($cacheItem->get());
        }

        $services = $this->repository->get($contractId, $limit, $skip);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $services->toArray());

        return $services;
    }

    /**
     * @throws QueryBuilderException
     * @throws ContractServiceNotFoundException
     */
    #[Override]
    public function getOne(ContractId $contractId, ServiceId $serviceId): ContractService
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromId($contractId, $serviceId));

        if ($cacheItem->isHit()) {
            return $this->convertor->convertToEntity($cacheItem->get());
        }

        $service = $this->repository->getOne($contractId, $serviceId);

        $this->saveToCache($cacheItem, $service->toArray());

        return $service;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(ContractService $contractService): void
    {
        $this->repository->add($contractService);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($contractService->getContractId()));
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(ContractService $contractService): void
    {
        $this->repository->update($contractService);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($contractService->getContractId(), $contractService->getServiceId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($contractService->getContractId()));
    }

    /**
     * @throws QueryBuilderException
     * @throws ContractServiceIsNotDeletedException
     */
    #[Override]
    public function delete(ContractService $contractService): void
    {
        $this->repository->delete($contractService);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($contractService->getContractId(), $contractService->getServiceId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($contractService->getContractId()));
    }
}
