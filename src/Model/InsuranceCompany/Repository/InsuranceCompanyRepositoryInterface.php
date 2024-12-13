<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Repository;

use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyCollection;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;

interface InsuranceCompanyRepositoryInterface
{
    public function get(int $limit, int $skip): InsuranceCompanyCollection;

    /**
     * @throws InsuranceCompanyNotFoundException
     */
    public function getOne(InsuranceCompanyId $id): InsuranceCompany;

    public function add(InsuranceCompany $insuranceCompany): void;

    public function update(InsuranceCompany $insuranceCompany): void;

    public function delete(InsuranceCompany $insuranceCompany): void;

    public function findOneByName(string $name): ?InsuranceCompany;

    public function findOneByEmail(Email $email): ?InsuranceCompany;

    public function findOneByAccessToken(string $accessToken): ?InsuranceCompany;

    public function findOneByEmailConfirmToken(string $token): ?InsuranceCompany;
}
