<?php

declare(strict_types=1);

namespace App\Model\Contract\Repository\Balance;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Model\Contract\Entity\Balance\Balance;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Model\Contract\Service\Balance\BalanceConvertor;
use App\Model\Contract\Service\Balance\CacheItemKeyGenerator;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\CacheTrait;
use App\Model\Contract\Exception\Balance\BalanceNotFoundException;

class CachedBalanceRepository implements BalanceRepositoryInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlBalanceRepository $repository,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly BalanceConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     * @throws BalanceNotFoundException
     */
    #[Override]
    public function getOne(InsuredPersonId $insuredPersonId, ServiceId $serviceId, LimitType $limitType): Balance
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromId($insuredPersonId, $serviceId, $limitType));

        if ($cacheItem->isHit()) {
            return $this->convertor->convertToEntity($cacheItem->get());
        }

        $balance = $this->repository->getOne($insuredPersonId, $serviceId, $limitType);

        $this->saveToCache($cacheItem, $balance->toArray());

        return $balance;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(Balance $balance): void
    {
        $this->repository->add($balance);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($balance->getInsuredPersonId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdateByContract($balance->getContractId()));
        $this->cache->deleteItem(
            $this->cacheItemKeyGenerator->generateFromId($balance->getInsuredPersonId(), $balance->getServiceId(), $balance->getLimitType())
        );
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(Balance $balance): void
    {
        $this->repository->update($balance);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($balance->getInsuredPersonId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdateByContract($balance->getContractId()));
        $this->cache->deleteItem(
            $this->cacheItemKeyGenerator->generateFromId($balance->getInsuredPersonId(), $balance->getServiceId(), $balance->getLimitType())
        );
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function save(Balance $balance): void
    {
        $this->repository->save($balance);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($balance->getInsuredPersonId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdateByContract($balance->getContractId()));
        $this->cache->deleteItem(
            $this->cacheItemKeyGenerator->generateFromId($balance->getInsuredPersonId(), $balance->getServiceId(), $balance->getLimitType())
        );
    }
}
