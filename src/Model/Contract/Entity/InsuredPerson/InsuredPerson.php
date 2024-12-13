<?php

declare(strict_types=1);

namespace App\Model\Contract\Entity\InsuredPerson;

use App\Model\AggregateRootInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Event\InsuredPerson\ExceedLimitAllowedEvent;
use App\Model\Contract\Event\InsuredPerson\ExceedLimitDisallowedEvent;
use App\Model\Contract\Event\InsuredPerson\InsuredPersonAddedEvent;
use App\Model\Contract\Event\InsuredPerson\InsuredPersonDeletedEvent;
use App\Model\Contract\Event\InsuredPerson\PolicyNumberChangedEvent;
use App\Model\EventTrait;
use InvalidArgumentException;

class InsuredPerson implements AggregateRootInterface
{
    use EventTrait;

    private string $policyNumber;
    private bool $isDeleted = false;

    public function __construct(
        private readonly InsuredPersonId $id,
        private readonly ContractId $contractId,
        private readonly PersonId $personId,
        string $policyNumber,
        private bool $isAllowedToExceedLimit
    ) {
        $this->assertPolicyNumberIsNotEmpty($policyNumber);
        $this->policyNumber = $policyNumber;

        $this->registerEvent(
            new InsuredPersonAddedEvent(
                $this->contractId,
                $this->id,
                $this->personId,
                $policyNumber,
                $this->isAllowedToExceedLimit
            )
        );
    }

    public function getId(): InsuredPersonId
    {
        return $this->id;
    }

    public function getContractId(): ContractId
    {
        return $this->contractId;
    }

    public function getPersonId(): PersonId
    {
        return $this->personId;
    }

    public function getPolicyNumber(): string
    {
        return $this->policyNumber;
    }

    public function isAllowedToExceedLimit(): bool
    {
        return $this->isAllowedToExceedLimit;
    }

    public function changePolicyNumber(string $newPolicyNumber): void
    {
        $this->assertPolicyNumberIsNotEmpty($newPolicyNumber);

        $oldPolicyNumber = $this->policyNumber;
        $this->policyNumber = $newPolicyNumber;

        if ($oldPolicyNumber !== $newPolicyNumber) {
            $this->registerEvent(
                new PolicyNumberChangedEvent(
                    $this->contractId,
                    $this->id,
                    $oldPolicyNumber,
                    $newPolicyNumber
                )
            );
        }
    }

    private function assertPolicyNumberIsNotEmpty(string $policyNumber): void
    {
        if (empty($policyNumber)) {
            throw new InvalidArgumentException('Не указан номер полиса');
        }
    }

    public function allowToExceedLimit(): void
    {
        $oldIsAllowedToExceedLimit = $this->isAllowedToExceedLimit;
        $this->isAllowedToExceedLimit = true;

        if ($oldIsAllowedToExceedLimit === false) {
            $this->registerEvent(
                new ExceedLimitAllowedEvent(
                    $this->contractId,
                    $this->id
                )
            );
        }
    }

    public function disallowToExceedLimit(): void
    {
        $oldIsAllowedToExceedLimit = $this->isAllowedToExceedLimit;
        $this->isAllowedToExceedLimit = false;

        if ($oldIsAllowedToExceedLimit === true) {
            $this->registerEvent(
                new ExceedLimitDisallowedEvent(
                    $this->contractId,
                    $this->id
                )
            );
        }
    }

    public function delete(): void
    {
        $isAlreadyDeleted = $this->isDeleted;
        $this->isDeleted = true;

        if (!$isAlreadyDeleted) {
            $this->registerEvent(
                new InsuredPersonDeletedEvent(
                    $this->contractId,
                    $this->id,
                    $this->personId,
                    $this->policyNumber
                )
            );
        }
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function toArray(): array
    {
        return [
            'id'                         => $this->id->getValue(),
            'contract_id'                => $this->contractId->getValue(),
            'person_id'                  => $this->personId->getValue(),
            'policy_number'              => $this->policyNumber,
            'is_allowed_to_exceed_limit' => ($this->isAllowedToExceedLimit ? 1 : 0),
        ];
    }
}
