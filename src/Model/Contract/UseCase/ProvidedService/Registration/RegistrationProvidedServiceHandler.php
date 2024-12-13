<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\ProvidedService\Registration;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\ProvidedService\Id;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Entity\ProvidedService\Service;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use App\Model\Contract\Repository\InsuredPerson\InsuredPersonRepositoryInterface;
use App\Model\Contract\Repository\ProvidedService\ProvidedServiceRepositoryInterface;
use App\Model\Contract\Specification\ProvidedService\CanBeRegisteredSpecification;
use App\Model\Service\Exception\ServiceNotFoundException;
use App\Model\Service\Repository\ServiceRepositoryInterface;

readonly class RegistrationProvidedServiceHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ProvidedServiceRepositoryInterface $providedServiceRepository, private InsuredPersonRepositoryInterface $insuredPersonRepository, private ContractServiceRepositoryInterface $contractServiceRepository, private ServiceRepositoryInterface $serviceRepository, private CanBeRegisteredSpecification $canBeRegisteredSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws ContractServiceNotFoundException
     * @throws InsuredPersonNotFoundException
     * @throws ServiceNotFoundException
     */
    public function handle(RegistrationProvidedServiceCommand $command): void
    {
        $providedService = $this->buildProvidedService($command);

        $this->canBeRegisteredSpecification->throwExceptionIfIsNotSatisfiedBy($providedService);

        $this->providedServiceRepository->add($providedService);

        $this->eventDispatcher->dispatchMany($providedService->releaseEvents());
    }

    /**
     * @throws ContractServiceNotFoundException
     * @throws InsuredPersonNotFoundException
     * @throws ServiceNotFoundException
     */
    private function buildProvidedService(RegistrationProvidedServiceCommand $command): ProvidedService
    {
        $insuredPerson = $this->insuredPersonRepository->getOne(new InsuredPersonId($command->insuredPersonId));
        $contractService = $this->contractServiceRepository->getOne(
            $insuredPerson->getContractId(),
            new ServiceId($command->serviceId)
        );
        $service = $this->serviceRepository->getOne(new \App\Model\Service\Entity\ServiceId($command->serviceId));

        return new ProvidedService(
            new Id($command->id),
            $contractService->getContractId(),
            new InsuredPersonId($command->insuredPersonId),
            $command->dateOfService,
            new Service(
                new ServiceId($command->serviceId),
                $service->getName(),
                $command->quantity,
                $command->price,
                $command->amount
            ),
            $contractService->getLimit()->getType()
        );
    }
}
