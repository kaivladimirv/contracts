<?php

declare(strict_types=1);

namespace App\Model\Person\Repository;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Model\Person\Exception\PersonIsNotDeletedException;
use App\Service\Cache\CacheTrait;
use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\Person;
use App\Model\Person\Entity\PersonCollection;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Entity\PhoneNumber;
use App\Model\Person\Exception\PersonNotFoundException;
use App\Model\Person\Service\CacheItemKeyGenerator;
use App\Model\Person\Service\PersonConvertor;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Etag;

class CachedPersonRepository implements PersonRepositoryInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlPersonRepository $repository,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly PersonConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip): PersonCollection
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate($insuranceCompanyId));
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFrom($insuranceCompanyId, __FUNCTION__, $limit, $skip));

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $this->convertor->convertToCollection($cacheItem->get());
        }

        $persons = $this->repository->get($insuranceCompanyId, $limit, $skip);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $persons->toArray());

        return $persons;
    }

    /**
     * @throws QueryBuilderException
     * @throws PersonNotFoundException
     */
    #[Override]
    public function getOne(PersonId $id): Person
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromId($id));

        if ($cacheItem->isHit()) {
            return $this->convertor->convertToEntity($cacheItem->get());
        }

        $person = $this->repository->getOne($id);

        $this->saveToCache($cacheItem, $person->toArray());

        return $person;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(Person $person): void
    {
        $this->repository->add($person);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($person->getInsuranceCompanyId()));
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(Person $person): void
    {
        $this->repository->update($person);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($person->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($person->getInsuranceCompanyId()));
    }

    /**
     * @throws QueryBuilderException
     * @throws PersonIsNotDeletedException
     */
    #[Override]
    public function delete(Person $person): void
    {
        $this->repository->delete($person);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($person->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate($person->getInsuranceCompanyId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromName($person->getInsuranceCompanyId(), $person->getName()));

        if ($person->getEmail()) {
            $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromEmail($person->getInsuranceCompanyId(), $person->getEmail()));
        }

        if ($person->getPhoneNumber()) {
            $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromPhoneNumber($person->getInsuranceCompanyId(), $person->getPhoneNumber()));
        }
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByName(InsuranceCompanyId $insuranceCompanyId, Name $name): ?Person
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromName($insuranceCompanyId, $name));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }

        $person = $this->repository->findByName($insuranceCompanyId, $name);

        $this->saveToCache($cacheItem, $person?->toArray());

        return $person;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByEmail(InsuranceCompanyId $insuranceCompanyId, Email $email): ?Person
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromEmail($insuranceCompanyId, $email));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }

        $person = $this->repository->findByEmail($insuranceCompanyId, $email);

        $this->saveToCache($cacheItem, $person?->toArray());

        return $person;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByPhoneNumber(InsuranceCompanyId $insuranceCompanyId, PhoneNumber $phoneNumber): ?Person
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromPhoneNumber($insuranceCompanyId, $phoneNumber));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }

        $person = $this->repository->findByPhoneNumber($insuranceCompanyId, $phoneNumber);

        $this->saveToCache($cacheItem, $person?->toArray());

        return $person;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByTelegramUserId(InsuranceCompanyId $insuranceCompanyId, string $userId): ?Person
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromTelegramUserId($insuranceCompanyId, $userId));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }

        $person = $this->repository->findByTelegramUserId($insuranceCompanyId, $userId);

        $this->saveToCache($cacheItem, $person?->toArray());

        return $person;
    }
}
