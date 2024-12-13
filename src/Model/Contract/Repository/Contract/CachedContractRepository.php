<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\Contract;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Model\Contract\Entity\Contract\Contract;
use App\Model\Contract\Entity\Contract\ContractCollection;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\Contract\Exception\Contract\ContractIsNotDeletedException;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\Service\Contract\CacheItemKeyGenerator;
use App\Model\Contract\Service\Contract\ContractConvertor;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\CacheTrait;
use App\Service\Cache\Etag;
use Exception;

class CachedContractRepository implements ContractRepositoryInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlContractRepository $repository,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly ContractConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function get(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip): ContractCollection
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuranceCompanyId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFrom($insuranceCompanyId, __FUNCTION__, $limit, $skip));

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $this->convertor->convertToCollection($cacheItem->get());
        }

        $contracts = $this->repository->get($insuranceCompanyId, $limit, $skip);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $contracts->toArray());

        return $contracts;
    }

    /**
     * @throws QueryBuilderException
     * @throws ContractNotFoundException
     * @throws Exception
     */
    #[Override]
    public function getOne(ContractId $id): Contract
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromId($id));

        if ($cacheItem->isHit()) {
            return $this->convertor->convertToEntity($cacheItem->get());
        }

        $contract = $this->repository->getOne($id);

        $this->saveToCache($cacheItem, $contract->toArray());

        return $contract;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(Contract $contract): void
    {
        $this->repository->add($contract);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($contract->getInsuranceCompanyId()));
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(Contract $contract): void
    {
        $this->repository->update($contract);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($contract->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($contract->getInsuranceCompanyId()));
    }

    /**
     * @throws QueryBuilderException
     * @throws ContractIsNotDeletedException
     */
    #[Override]
    public function delete(Contract $contract): void
    {
        $this->repository->delete($contract);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($contract->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($contract->getInsuranceCompanyId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromNumber($contract->getInsuranceCompanyId(), $contract->getNumber()));
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function findByNumber(InsuranceCompanyId $insuranceCompanyId, string $number): ?Contract
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromNumber($insuranceCompanyId, $number));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }

        $contract = $this->repository->findByNumber($insuranceCompanyId, $number);

        $this->saveToCache($cacheItem, $contract?->toArray());

        return $contract;
    }
}
