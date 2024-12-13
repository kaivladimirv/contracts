<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\InsuredPerson;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonCollection;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\InsuredPerson\PersonId;
use App\Service\Hydrator\HydratorInterface;

readonly class InsuredPersonConvertor
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private HydratorInterface $hydrator)
    {
    }

    public function convertToEntity(array $data): InsuredPerson
    {
        $data = [
            'id'                     => new InsuredPersonId($data['id']),
            'contractId'             => new ContractId($data['contract_id']),
            'personId'               => new PersonId($data['person_id']),
            'policyNumber'           => $data['policy_number'],
            'isAllowedToExceedLimit' => (bool) $data['is_allowed_to_exceed_limit'],
        ];

        /* @var InsuredPerson $insuredPerson */
        $insuredPerson = $this->hydrator->hydrate(InsuredPerson::class, $data);

        return $insuredPerson;
    }

    public function convertToCollection(array $data): InsuredPersonCollection
    {
        $collection = new InsuredPersonCollection();

        foreach ($data as $value) {
            $collection->append($this->convertToEntity($value));
        }

        return $collection;
    }
}
