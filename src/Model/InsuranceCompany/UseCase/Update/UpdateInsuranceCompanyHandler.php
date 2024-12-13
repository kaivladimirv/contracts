<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\UseCase\Update;

use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\Exception\NameNotSpecifiedException;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;
use App\Model\InsuranceCompany\Specification\CanBeRegisteredOrUpdatedSpecification;
use App\Event\Dispatcher\EventDispatcherInterface;

readonly class UpdateInsuranceCompanyHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private InsuranceCompanyRepositoryInterface $insuranceCompanyRepository, private CanBeRegisteredOrUpdatedSpecification $canBeRegisteredOrUpdatedSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws InsuranceCompanyNotFoundException
     * @throws NameNotSpecifiedException
     */
    public function handle(UpdateInsuranceCompanyCommand $command): void
    {
        $insuranceCompany = $this->insuranceCompanyRepository->getOne(new InsuranceCompanyId($command->id));

        $insuranceCompany->changeName($command->name);

        $this->canBeRegisteredOrUpdatedSpecification->throwExceptionIfIsNotSatisfiedBy($insuranceCompany);

        $this->insuranceCompanyRepository->update($insuranceCompany);

        $this->eventDispatcher->dispatchMany($insuranceCompany->releaseEvents());
    }
}
