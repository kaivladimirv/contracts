<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Contract\Delete;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;

readonly class DeleteContractHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ContractRepositoryInterface $contractRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws ContractNotFoundException
     */
    public function handle(DeleteContractCommand $command): void
    {
        $contract = $this->contractRepository->getOne(new ContractId($command->id));

        $contract->delete();

        $this->contractRepository->delete($contract);

        $this->eventDispatcher->dispatchMany($contract->releaseEvents());
    }
}
