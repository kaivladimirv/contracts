<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ProvidedService\CancelRegistration;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Contract\Entity\ProvidedService\Id;
use App\Model\Contract\Repository\ProvidedService\ProvidedServiceRepositoryInterface;

readonly class CancelRegistrationProvidedServiceHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ProvidedServiceRepositoryInterface $providedServiceRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function handle(CancelRegistrationProvidedServiceCommand $command): void
    {
        $providedService = $this->providedServiceRepository->getOne(new Id($command->id));

        $providedService->cancelRegistration($command->date);

        $this->providedServiceRepository->update($providedService);

        $this->eventDispatcher->dispatchMany($providedService->releaseEvents());
    }
}
