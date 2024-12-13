<?php

declare(strict_types=1);

namespace App\Model\Service\UseCase\Delete;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Service\Entity\ServiceId;
use App\Model\Service\Exception\ServiceNotFoundException;
use App\Model\Service\Repository\ServiceRepositoryInterface;

readonly class DeleteServiceHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ServiceRepositoryInterface $serviceRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function handle(DeleteServiceCommand $command): void
    {
        $service = $this->serviceRepository->getOne(new ServiceId($command->id));

        $service->delete();

        $this->serviceRepository->delete($service);

        $this->eventDispatcher->dispatchMany($service->releaseEvents());
    }
}
