<?php

declare(strict_types=1);

namespace App\Model\Person\Repository;

use Override;
use App\Framework\Database\Exception\QueryBuilderException;
use App\Framework\Database\QueryBuilder;
use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\Person;
use App\Model\Person\Entity\PersonCollection;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Entity\PhoneNumber;
use App\Model\Person\Exception\PersonIsNotDeletedException;
use App\Model\Person\Exception\PersonNotFoundException;
use App\Model\Person\Service\PersonConvertor;

class PostgresqlPersonRepository implements PersonRepositoryInterface
{
    private const string TABLE_NAME = 'persons';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly QueryBuilder $queryBuilder, private readonly PersonConvertor $convertor)
    {
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function get(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip): PersonCollection
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->limit($limit)
            ->skip($skip)
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->orderBy(
                [
                    'last_name',
                    'first_name',
                    'middle_name',
                ]
            )
            ->fetchAll();

        return $this->convertor->convertToCollection($data);
    }

    /**
     * @throws QueryBuilderException
     * @throws PersonNotFoundException
     */
    #[Override]
    public function getOne(PersonId $id): Person
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['id' => $id->getValue()])
            ->fetch();

        if (!$data) {
            throw new PersonNotFoundException('Персона не найдена');
        }

        return $this->convertor->convertToEntity($data);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function add(Person $person): void
    {
        $data = $person->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->insert($data)
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function update(Person $person): void
    {
        $data = $person->toArray();

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->update($data)
            ->andWhere(['id' => $person->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     * @throws PersonIsNotDeletedException
     */
    #[Override]
    public function delete(Person $person): void
    {
        if (!$person->isDeleted()) {
            throw new PersonIsNotDeletedException('Персона не отмечена как удаленная');
        }

        $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->delete()
            ->andWhere(['id' => $person->getId()->getValue()])
            ->execute();
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByName(InsuranceCompanyId $insuranceCompanyId, Name $name): ?Person
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->andWhere(['last_name' => $name->getLastName()])
            ->andWhere(['first_name' => $name->getFirstName()])
            ->andWhere(['middle_name' => $name->getMiddleName()])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByEmail(InsuranceCompanyId $insuranceCompanyId, Email $email): ?Person
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->andWhere(['email' => $email->getValue()])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByPhoneNumber(InsuranceCompanyId $insuranceCompanyId, PhoneNumber $phoneNumber): ?Person
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->andWhere(['phone_number' => $phoneNumber->getValue()])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }

    /**
     * @throws QueryBuilderException
     */
    #[Override]
    public function findByTelegramUserId(InsuranceCompanyId $insuranceCompanyId, string $userId): ?Person
    {
        $data = $this->queryBuilder
            ->table(self::TABLE_NAME)
            ->select(['*'])
            ->andWhere(['insurance_company_id' => $insuranceCompanyId->getValue()])
            ->andWhere(['telegram_user_id' => $userId])
            ->fetch();

        return ($data ? $this->convertor->convertToEntity($data) : null);
    }
}
