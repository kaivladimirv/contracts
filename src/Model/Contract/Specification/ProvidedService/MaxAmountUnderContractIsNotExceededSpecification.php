<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\ProvidedService;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;
use App\ReadModel\ProvidedService\ProvidedServiceFetcherInterface;

class MaxAmountUnderContractIsNotExceededSpecification extends AbstractSpecification
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly ContractRepositoryInterface $contractRepository, private readonly ProvidedServiceFetcherInterface $providedServiceFetcher)
    {
    }

    /**
     * @throws ContractNotFoundException
     */
    #[Override]
    public function isSatisfiedBy($entity): bool
    {
        /** @var ProvidedService $entity */

        $contract = $this->contractRepository->getOne($entity->getContractId());

        $amountOfServicesProvided = $this->providedServiceFetcher->getAmountByInsuredPerson(
            $entity->getInsuredPersonId()
        );

        if (($amountOfServicesProvided + $entity->getService()->getAmount()) <= $contract->getMaxAmount()) {
            return true;
        }

        $this->reasonNotSatisfied = sprintf(
            'Сумма оказанных услуг превышает максимальную сумму по договору. '
            . 'Максимальная сумма по договору %s. '
            . 'Остаток %s.',
            $contract->getMaxAmount(),
            $contract->getMaxAmount() - $amountOfServicesProvided
        );

        return false;
    }
}
