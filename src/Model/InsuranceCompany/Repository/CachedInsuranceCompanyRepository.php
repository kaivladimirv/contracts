<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Repository;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Service\Cache\CacheTrait;
use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyCollection;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\Service\CacheItemKeyGenerator;
use App\Model\InsuranceCompany\Service\InsuranceCompanyConvertor;
use App\Service\Cache\CacheItemPoolInterface;
use App\Service\Cache\Etag;
use Exception;

class CachedInsuranceCompanyRepository implements InsuranceCompanyRepositoryInterface
{
    use CacheTrait;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly PostgresqlInsuranceCompanyRepository $repository,
        private readonly CacheItemPoolInterface $cache,
        private readonly CacheItemKeyGenerator $cacheItemKeyGenerator,
        private readonly InsuranceCompanyConvertor $convertor
    ) {
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function get(int $limit, int $skip): InsuranceCompanyCollection
    {
        $etagCacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateForLastUpdate());
        $etag = $etagCacheItem->isHit() ? new Etag($etagCacheItem->get()) : Etag::next();

        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFrom(__FUNCTION__, $limit, $skip));

        if ($cacheItem->isHit() and $cacheItem->isEtagEqual($etag)) {
            return $this->convertor->convertToCollection($cacheItem->get());
        }

        $insuranceCompanies = $this->repository->get($limit, $skip);

        if (!$etagCacheItem->isHit() or !$cacheItem->isEtagEqual($etag)) {
            $this->saveToCache($etagCacheItem, $etag->getValue());
        }
        $cacheItem->setEtag($etag);
        $this->saveToCache($cacheItem, $insuranceCompanies->toArray());

        return $insuranceCompanies;
    }

    /**
     * @throws QueryBuilderException
     * @throws InsuranceCompanyNotFoundException
     * @throws Exception
     */
    #[Override]
    public function getOne(InsuranceCompanyId $id): InsuranceCompany
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromId($id));

        if ($cacheItem->isHit()) {
            return $this->convertor->convertToEntity($cacheItem->get());
        }

        $insuranceCompany = $this->repository->getOne($id);

        $this->saveToCache($cacheItem, $insuranceCompany->toArray());

        return $insuranceCompany;
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(InsuranceCompany $insuranceCompany): void
    {
        $this->repository->add($insuranceCompany);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate());
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function update(InsuranceCompany $insuranceCompany): void
    {
        $this->repository->update($insuranceCompany);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($insuranceCompany->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate());
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function delete(InsuranceCompany $insuranceCompany): void
    {
        $this->repository->delete($insuranceCompany);

        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromId($insuranceCompany->getId()));
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateForLastUpdate());
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function findOneByName(string $name): ?InsuranceCompany
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromName($name));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }
        $insuranceCompany = $this->repository->findOneByName($name);

        $this->saveToCache($cacheItem, $insuranceCompany?->toArray());

        return $insuranceCompany;
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function findOneByEmail(Email $email): ?InsuranceCompany
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromEmail($email->getValue()));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }

        $insuranceCompany = $this->repository->findOneByEmail($email);

        $this->saveToCache($cacheItem, $insuranceCompany?->toArray());

        return $insuranceCompany;
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function findOneByAccessToken(string $accessToken): ?InsuranceCompany
    {
        $cacheItem = $this->cache->getItem($this->cacheItemKeyGenerator->generateFromAccessToken($accessToken));

        if ($cacheItem->isHit()) {
            return $cacheItem->get() ? $this->convertor->convertToEntity($cacheItem->get()) : $cacheItem->get();
        }

        $insuranceCompany = $this->repository->findOneByAccessToken($accessToken);

        $this->saveToCache($cacheItem, $insuranceCompany?->toArray());

        return $insuranceCompany;
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function findOneByEmailConfirmToken(string $token): ?InsuranceCompany
    {
        return $this->repository->findOneByEmailConfirmToken($token);
    }
}
