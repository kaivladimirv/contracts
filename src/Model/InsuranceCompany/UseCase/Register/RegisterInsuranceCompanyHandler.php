<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\UseCase\Register;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\InsuranceCompany\Entity\Email;
use App\Model\InsuranceCompany\Entity\InsuranceCompany;
use App\Model\InsuranceCompany\Entity\InsuranceCompanyId;
use App\Model\InsuranceCompany\Repository\InsuranceCompanyRepositoryInterface;
use App\Model\InsuranceCompany\Service\EmailConfirmTokenGenerator;
use App\Model\InsuranceCompany\Service\PasswordHasher;
use App\Model\InsuranceCompany\Specification\CanBeRegisteredOrUpdatedSpecification;
use App\Service\Queue\QueueClientInterface;
use Exception;

readonly class RegisterInsuranceCompanyHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private InsuranceCompanyRepositoryInterface $insuranceCompanyRepository, private EmailConfirmTokenGenerator $tokenGenerator, private QueueClientInterface $queueClient, private PasswordHasher $passwordHasher, private CanBeRegisteredOrUpdatedSpecification $canBeRegisteredOrUpdatedSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws Exception
     */
    public function handle(RegisterInsuranceCompanyCommand $command): void
    {
        $insuranceCompany = $this->buildInsuranceCompany($command);

        $this->canBeRegisteredOrUpdatedSpecification->throwExceptionIfIsNotSatisfiedBy($insuranceCompany);

        $this->insuranceCompanyRepository->add($insuranceCompany);

        $this->sendEmail($insuranceCompany->getEmailConfirmToken(), $insuranceCompany->getEmail());

        $this->eventDispatcher->dispatchMany($insuranceCompany->releaseEvents());
    }

    /**
     * @throws Exception
     */
    private function buildInsuranceCompany(RegisterInsuranceCompanyCommand $command): InsuranceCompany
    {
        return InsuranceCompany::register(
            new InsuranceCompanyId($command->id),
            $command->name,
            new Email($command->email),
            $this->passwordHasher->hash($command->password),
            $this->tokenGenerator->generate()
        );
    }

    private function sendEmail(string $token, Email $email): void
    {
        $this->queueClient->connect();
        $this->queueClient->publish(
            'send-email-confirm-token',
            [
                'email' => $email->getValue(),
                'token' => $token,
            ]
        );
        $this->queueClient->disconnect();
    }
}
