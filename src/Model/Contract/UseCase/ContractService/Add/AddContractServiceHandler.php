<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ContractService\Add;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ContractService;
use App\Model\Contract\Entity\ContractService\ContractServiceId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\Limit\Limit;
use App\Model\Contract\Entity\Limit\LimitType;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use App\Model\Contract\Specification\ContractService\CanBeAddedSpecification;

readonly class AddContractServiceHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private ContractServiceRepositoryInterface $contractServiceRepository,
        private CanBeAddedSpecification $canBeAddedSpecification,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function handle(AddContractServiceCommand $command): void
    {
        $contractService = $this->buildContractService($command);

        $this->canBeAddedSpecification->throwExceptionIfIsNotSatisfiedBy($contractService);

        $this->contractServiceRepository->add($contractService);

        $this->eventDispatcher->dispatchMany($contractService->releaseEvents());
    }

    private function buildContractService(AddContractServiceCommand $command): ContractService
    {
        return new ContractService(
            new ContractServiceId($command->id),
            new ContractId($command->contractId),
            new ServiceId($command->serviceId),
            new Limit(new LimitType($command->limitType), $command->limitValue)
        );
    }
}
