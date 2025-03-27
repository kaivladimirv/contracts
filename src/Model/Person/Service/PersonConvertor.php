<?php

declare(strict_types=1);

namespace App\Model\Person\Service;

use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\NotifierType;
use App\Model\Person\Entity\Person;
use App\Model\Person\Entity\PersonCollection;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Entity\PhoneNumber;
use App\Service\Hydrator\HydratorInterface;

readonly class PersonConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    public function convertToEntity(array $data): Person
    {
        $data = [
            'id'                 => new PersonId($data['id']),
            'name'               => new Name($data['last_name'], $data['first_name'], $data['middle_name']),
            'insuranceCompanyId' => new InsuranceCompanyId($data['insurance_company_id']),
            'email'              => $data['email'] ? new Email($data['email']) : null,
            'phoneNumber'        => $data['phone_number'] ? new PhoneNumber($data['phone_number']) : null,
            'telegramUserId'     => $data['telegram_user_id'],
            'notifierType'       => !is_null($data['notifier_type']) ? new NotifierType($data['notifier_type']) : null,
        ];

        return $this->hydrator->hydrate(Person::class, $data);
    }

    public function convertToCollection(array $data): PersonCollection
    {
        $collection = new PersonCollection();

        foreach ($data as $value) {
            $collection->append($this->convertToEntity($value));
        }

        return $collection;
    }
}
