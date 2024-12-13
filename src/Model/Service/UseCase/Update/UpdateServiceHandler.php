<?php

declare(strict_types=1);

namespace App\Model\Service\UseCase\Update;

use App\Model\Service\Entity\ServiceId;
use App\Model\Service\Exception\ServiceNotFoundException;
use App\Model\Service\Repository\ServiceRepositoryInterface;
use App\Model\Service\Specification\NameIsUniqueSpecification;
use App\Event\Dispatcher\EventDispatcherInterface;

readonly class UpdateServiceHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ServiceRepositoryInterface $serviceRepository, private NameIsUniqueSpecification $nameIsUniqueSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function handle(UpdateServiceCommand $command): void
    {
        $service = $this->serviceRepository->getOne(new ServiceId($command->id));

        $service->changeName($command->name);

        $this->nameIsUniqueSpecification->throwExceptionIfIsNotSatisfiedBy($service);

        $this->serviceRepository->update($service);

        $this->eventDispatcher->dispatchMany($service->releaseEvents());
    }
}
