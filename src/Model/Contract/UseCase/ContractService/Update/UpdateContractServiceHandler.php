<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ContractService\Update;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\Limit\Limit;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;

readonly class UpdateContractServiceHandler
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
    public function handle(UpdateContractServiceCommand $command): void
    {
        $contractService = $this->contractServiceRepository->getOne(
            new ContractId($command->contractId),
            new ServiceId($command->serviceId)
        );

        $limit = new Limit($contractService->getLimit()->getType(), $command->limitValue);

        $contractService->changeLimit($limit);

        $this->contractServiceRepository->update($contractService);

        $this->eventDispatcher->dispatchMany($contractService->releaseEvents());
    }
}
