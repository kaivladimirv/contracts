<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Service;

use App\Model\InsuranceCompany\Entity\AccessToken;
use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyCollection;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Service\Hydrator\HydratorInterface;
use DateTimeImmutable;
use Exception;

readonly class InsuranceCompanyConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    /**
     * @throws Exception
     */
    public function convertToEntity(array $data): InsuranceCompany
    {
        $data = [
            'id'                => new InsuranceCompanyId($data['id']),
            'name'              => $data['name'],
            'email'             => new Email($data['email']),
            'passwordHash'      => $data['password_hash'],
            'emailConfirmToken' => $data['email_confirm_token'],
            'isEmailConfirmed'  => (bool) $data['is_email_confirmed'],
            'accessToken'       => $data['access_token'] ? new AccessToken($data['access_token'], new DateTimeImmutable($data['access_token_expires'])) : null,
            'isDeleted'         => (bool) $data['is_deleted'],
        ];

        return $this->hydrator->hydrate(InsuranceCompany::class, $data);
    }

    /**
     * @throws Exception
     */
    public function convertToCollection(array $data): InsuranceCompanyCollection
    {
        $collection = new InsuranceCompanyCollection();

        foreach ($data as $value) {
            $collection->append($this->convertToEntity($value));
        }

        return $collection;
    }
}
