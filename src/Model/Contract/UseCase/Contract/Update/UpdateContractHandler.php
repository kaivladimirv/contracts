<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Contract\Update;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\Contract\Period;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;
use App\Model\Contract\Specification\Contract\ContractNumberIsUniqueSpecification;
use App\Event\Dispatcher\EventDispatcherInterface;

readonly class UpdateContractHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ContractRepositoryInterface $contractRepository, private ContractNumberIsUniqueSpecification $contractNumberIsUniqueSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws ContractNotFoundException
     */
    public function handle(UpdateContractCommand $command): void
    {
        $contract = $this->contractRepository->getOne(new ContractId($command->id));

        $contract->changeNumber($command->number);
        $contract->changePeriod(new Period($command->startDate, $command->endDate));
        $contract->changeMaxAmount($command->maxAmount);

        $this->contractNumberIsUniqueSpecification->throwExceptionIfIsNotSatisfiedBy($contract);

        $this->contractRepository->update($contract);

        $this->eventDispatcher->dispatchMany($contract->releaseEvents());
    }
}
