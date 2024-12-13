<?php

declare(strict_types=1);

namespace App\Model\Person\Repository;

use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\Person;
use App\Model\Person\Entity\PersonCollection;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Entity\PhoneNumber;
use App\Model\Person\Exception\PersonNotFoundException;

interface PersonRepositoryInterface
{
    public function get(InsuranceCompanyId $insuranceCompanyId, int $limit, int $skip): PersonCollection;

    /**
     * @throws PersonNotFoundException
     */
    public function getOne(PersonId $id): Person;

    public function add(Person $person): void;

    public function update(Person $person): void;

    public function delete(Person $person): void;

    public function findByName(InsuranceCompanyId $insuranceCompanyId, Name $name): ?Person;

    public function findByEmail(InsuranceCompanyId $insuranceCompanyId, Email $email): ?Person;

    public function findByPhoneNumber(InsuranceCompanyId $insuranceCompanyId, PhoneNumber $phoneNumber): ?Person;

    public function findByTelegramUserId(InsuranceCompanyId $insuranceCompanyId, string $userId): ?Person;
}
