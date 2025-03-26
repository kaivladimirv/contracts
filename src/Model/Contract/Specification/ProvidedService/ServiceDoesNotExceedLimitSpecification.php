<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\ProvidedService;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;
use App\Model\Contract\Repository\Balance\BalanceRepositoryInterface;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use App\Model\Contract\Repository\InsuredPerson\InsuredPersonRepositoryInterface;
use App\Model\Contract\Exception\Balance\BalanceNotFoundException;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;

class ServiceDoesNotExceedLimitSpecification extends AbstractSpecification
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly ContractRepositoryInterface $contractRepository, private readonly InsuredPersonRepositoryInterface $insuredPersonRepository, private readonly ContractServiceRepositoryInterface $contractServiceRepository, private readonly BalanceRepositoryInterface $balanceRepository)
    {
    }

    /**
     * @throws ContractServiceNotFoundException
     * @throws BalanceNotFoundException
     * @throws InsuredPersonNotFoundException
     * @throws ContractNotFoundException
     */
    #[Override]
    public function isSatisfiedBy($entity): bool
    {
        /** @var ProvidedService $entity */

        $insuredPerson = $this->insuredPersonRepository->getOne($entity->getInsuredPersonId());
        if ($insuredPerson->isAllowedToExceedLimit()) {
            return true;
        }

        $contract = $this->contractRepository->getOne($entity->getContractId());
        $service = $this->contractServiceRepository->getOne($contract->getId(), $entity->getService()->getId());
        $balance = $this->balanceRepository->getOne(
            $entity->getInsuredPersonId(),
            $entity->getService()->getId(),
            $entity->getLimitType()
        );

        if ($service->getLimit()->getType()->isItAmountLimiter()) {
            if ($balance->getValue() < $entity->getService()->getAmount()) {
                $this->reasonNotSatisfied = sprintf(
                    'Сумма оказанной услуги превышает лимит по договору. ' .
                    'Лимит по договору %s. ' .
                    'Остаток %s.',
                    $service->getLimit()->getValue(),
                    $balance->getValue()
                );

                return false;
            }
        }

        if ($service->getLimit()->getType()->isItQuantityLimiter()) {
            if ($balance->getValue() < $entity->getService()->getQuantity()) {
                $this->reasonNotSatisfied = sprintf(
                    'Количество оказанной услуги превышает лимит по договору. ' .
                    'Лимит по договору %s. ' .
                    'Остаток %s.',
                    $service->getLimit()->getValue(),
                    $balance->getValue()
                );

                return false;
            }
        }

        return true;
    }
}
