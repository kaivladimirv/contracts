<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\UseCase\Delete;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;

readonly class DeleteInsuranceCompanyHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private InsuranceCompanyRepositoryInterface $insuranceCompanyRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws InsuranceCompanyNotFoundException
     */
    public function handle(DeleteInsuranceCompanyCommand $command): void
    {
        $insuranceCompany = $this->insuranceCompanyRepository->getOne(new InsuranceCompanyId($command->id));

        $insuranceCompany->delete();

        $this->insuranceCompanyRepository->delete($insuranceCompany);

        $this->eventDispatcher->dispatchMany($insuranceCompany->releaseEvents());
    }
}
