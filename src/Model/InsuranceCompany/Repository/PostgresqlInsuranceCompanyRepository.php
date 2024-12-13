<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Repository;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyCollection;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyIsNotDeletedException;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\Service\InsuranceCompanyConvertor;
use Exception;

class PostgresqlInsuranceCompanyRepository implements InsuranceCompanyRepositoryInterface
{
    private const string TABLE_NAME = 'insurance_companies';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly QueryBuilder $queryBuilder, private readonly InsuranceCompanyConvertor $convertor)
    {
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function get(int $limit, int $skip): InsuranceCompanyCollection
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->limit($limit)
            ->skip($skip)
            ->orderBy(['name'])
            ->fetchAll();

        return $this->convertor->convertToCollection($data);
    }

    /**
     * @throws QueryBuilderException
     * @throws InsuranceCompanyNotFoundException
     * @throws Exception
     */
    #[Override]
    public function getOne(InsuranceCompanyId $id): InsuranceCompany
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['id' => $id->getValue()])
            ->fetch();

        if (!$data) {
            throw new InsuranceCompanyNotFoundException('Страховая компания не найден');
        }

        return $this->convertor->convertToEntity($data);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(InsuranceCompany $insuranceCompany): void
    {
        $data = $insuranceCompany->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->insert($data)
            ->execute();
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function update(InsuranceCompany $insuranceCompany): void
    {
        $data = $insuranceCompany->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->update($data)
            ->andWhere(['id' => $insuranceCompany->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function delete(InsuranceCompany $insuranceCompany): void
    {
        if (!$insuranceCompany->isDeleted()) {
            throw new InsuranceCompanyIsNotDeletedException('Компания не отмечена как удаленная');
        }

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->update(['is_deleted' => 1])
            ->andWhere(['id' => $insuranceCompany->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function findOneByName(string $name): ?InsuranceCompany
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['name' => $name])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function findOneByEmail(Email $email): ?InsuranceCompany
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['email' => $email->getValue()])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function findOneByAccessToken(string $accessToken): ?InsuranceCompany
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['access_token' => $accessToken])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }

    /**
     * @throws QueryBuilderException
     * @throws Exception
     */
    #[Override]
    public function findOneByEmailConfirmToken(string $token): ?InsuranceCompany
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['email_confirm_token' => $token])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }
}
