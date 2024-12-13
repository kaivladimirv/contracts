<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\InsuredPerson;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonCollection;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\InsuredPerson\PersonId;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonDeleteException;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;
use App\Model\Contract\Service\InsuredPerson\CacheItemKeyGenerator;
use App\Model\Contract\Service\InsuredPerson\InsuredPersonConvertor;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\CacheTrait;
use App\Service\Cache\Etag;

class CachedInsuredPersonRepository implements InsuredPersonRepositoryInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlInsuredPersonRepository $repository,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly InsuredPersonConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(ContractId $contractId, int $limit, int $skip): InsuredPersonCollection
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($contractId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFrom($contractId, __FUNCTION__, $limit, $skip));

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $this->convertor->convertToCollection($cacheItem->get());
        }

        $insuredPersons = $this->repository->get($contractId, $limit, $skip);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $insuredPersons->toArray());

        return $insuredPersons;
    }

    /**
     * @throws QueryBuilderException
     * @throws InsuredPersonNotFoundException
     */
    #[Override]
    public function getOne(InsuredPersonId $insuredPersonId): InsuredPerson
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromId($insuredPersonId));

        if ($cacheItem->isHit()) {
            return $this->convertor->convertToEntity($cacheItem->get());
        }

        $insuredPerson = $this->repository->getOne($insuredPersonId);

        $this->saveToCache($cacheItem, $insuredPerson->toArray());

        return $insuredPerson;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(InsuredPerson $insuredPerson): void
    {
        $this->repository->add($insuredPerson);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPerson->getContractId()));
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(InsuredPerson $insuredPerson): void
    {
        $this->repository->update($insuredPerson);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($insuredPerson->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPerson->getContractId()));
    }

    /**
     * @throws QueryBuilderException
     * @throws InsuredPersonDeleteException
     */
    #[Override]
    public function delete(InsuredPerson $insuredPerson): void
    {
        $this->repository->delete($insuredPerson);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($insuredPerson->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuredPerson->getContractId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromPolicyNumber($insuredPerson->getContractId(), $insuredPerson->getPolicyNumber()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromPerson($insuredPerson->getContractId(), $insuredPerson->getPersonId()));
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByPolicyNumber(ContractId $contractId, string $policyNumber): ?InsuredPerson
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromPolicyNumber($contractId, $policyNumber));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }

        $insuredPerson = $this->repository->findByPolicyNumber($contractId, $policyNumber);

        $this->saveToCache($cacheItem, $insuredPerson?->toArray());

        return $insuredPerson;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByPersonId(ContractId $contractId, PersonId $personId): ?InsuredPerson
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromPerson($contractId, $personId));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }

        $insuredPerson = $this->repository->findByPersonId($contractId, $personId);

        $this->saveToCache($cacheItem, $insuredPerson?->toArray());

        return $insuredPerson;
    }
}
