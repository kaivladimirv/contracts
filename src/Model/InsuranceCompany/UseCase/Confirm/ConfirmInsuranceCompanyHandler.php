<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\UseCase\Confirm;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\InsuranceCompany\Exception\IncorrectConfirmTokenException;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;

readonly class ConfirmInsuranceCompanyHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private InsuranceCompanyRepositoryInterface $repository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws InsuranceCompanyNotFoundException
     * @throws IncorrectConfirmTokenException
     */
    public function handle(ConfirmInsuranceCompanyCommand $command): void
    {
        $insuranceCompany = $this->repository->findOneByEmailConfirmToken($command->emailConfirmToken);
        if (empty($insuranceCompany)) {
            throw new InsuranceCompanyNotFoundException('Страховая компания не найден');
        }

        $insuranceCompany->confirmRegister($command->emailConfirmToken);

        $this->repository->update($insuranceCompany);

        $this->eventDispatcher->dispatchMany($insuranceCompany->releaseEvents());
    }
}
