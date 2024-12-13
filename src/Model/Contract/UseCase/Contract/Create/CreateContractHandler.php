<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Contract\Create;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\Contract;
use App\Model\Contract\Entity\Contract\InsuranceCompanyId;
use App\Model\Contract\Entity\Contract\Period;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;
use App\Model\Contract\Specification\Contract\ContractNumberIsUniqueSpecification;

readonly class CreateContractHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ContractRepositoryInterface $contractRepository, private ContractNumberIsUniqueSpecification $contractNumberIsUniqueSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function handle(CreateContractCommand $command): void
    {
        $contract = $this->buildContract($command);

        $this->contractNumberIsUniqueSpecification->throwExceptionIfIsNotSatisfiedBy($contract);

        $this->contractRepository->add($contract);

        $this->eventDispatcher->dispatchMany($contract->releaseEvents());
    }

    private function buildContract(CreateContractCommand $command): Contract
    {
        return Contract::create(
            new ContractId($command->id),
            $command->number,
            new InsuranceCompanyId($command->insuranceCompanyId),
            new Period($command->startDate, $command->endDate),
            $command->maxAmount
        );
    }
}
