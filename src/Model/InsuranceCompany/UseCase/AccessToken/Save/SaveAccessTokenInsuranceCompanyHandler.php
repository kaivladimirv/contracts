<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\UseCase\AccessToken\Save;

use App\Model\InsuranceCompany\Entity\AccessToken;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Exception\AccessTokenIsExpiredException;
use App\Model\InsuranceCompany\Exception\InsuranceCompanyNotFoundException;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;
use App\Event\Dispatcher\EventDispatcherInterface;
use DateMalformedStringException;
use DateTimeImmutable;

readonly class SaveAccessTokenInsuranceCompanyHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private InsuranceCompanyRepositoryInterface $repository, private EventDispatcherInterface $eventDispatcher)
    {
    }


    /**
     * @throws AccessTokenIsExpiredException
     * @throws DateMalformedStringException
     * @throws InsuranceCompanyNotFoundException
     */
    public function handle(SaveAccessTokenInsuranceCompanyCommand $command): void
    {
        $insuranceCompany = $this->repository->getOne(new InsuranceCompanyId($command->insuranceCompanyId));

        $accessToken = new AccessToken($command->accessToken, new DateTimeImmutable($command->expires));

        $insuranceCompany->changeAccessToken($accessToken, new DateTimeImmutable());

        $this->repository->update($insuranceCompany);

        $this->eventDispatcher->dispatchMany($insuranceCompany->releaseEvents());
    }
}
