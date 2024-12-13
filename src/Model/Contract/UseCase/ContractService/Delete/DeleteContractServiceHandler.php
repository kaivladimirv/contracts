<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ContractService\Delete;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;

readonly class DeleteContractServiceHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ContractServiceRepositoryInterface $contractServiceRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws ContractServiceNotFoundException
     */
    public function handle(DeleteContractServiceCommand $command): void
    {
        $contractService = $this->contractServiceRepository->getOne(
            new ContractId($command->contractId),
            new ServiceId($command->serviceId)
        );

        $contractService->delete();

        $this->contractServiceRepository->delete($contractService);

        $this->eventDispatcher->dispatchMany($contractService->releaseEvents());
    }
}
