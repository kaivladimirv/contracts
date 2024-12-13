<?php

declare(strict_types=1);

namespace App\Model\Service\UseCase\Add;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\Service;
use App\Model\Service\Entity\ServiceId;
use App\Model\Service\Repository\ServiceRepositoryInterface;
use App\Model\Service\Specification\NameIsUniqueSpecification;

readonly class AddServiceHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ServiceRepositoryInterface $serviceRepository, private NameIsUniqueSpecification $nameIsUniqueSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function handle(AddServiceCommand $command): void
    {
        $service = $this->buildService($command);

        $this->nameIsUniqueSpecification->throwExceptionIfIsNotSatisfiedBy($service);

        $this->serviceRepository->add($service);

        $this->eventDispatcher->dispatchMany($service->releaseEvents());
    }

    private function buildService(AddServiceCommand $command): Service
    {
        return Service::create(
            new ServiceId($command->id),
            $command->name,
            new InsuranceCompanyId($command->insuranceCompanyId)
        );
    }
}
