<?php

declare(strict_types=1);

namespace App\Model\Person\UseCase\Add;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\InsuranceCompanyId;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\NotifierType;
use App\Model\Person\Entity\Person;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Entity\PhoneNumber;
use App\Model\Person\Repository\PersonRepositoryInterface;
use App\Model\Person\Specification\CanBeAddedOrUpdatedSpecification;

readonly class AddPersonHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private PersonRepositoryInterface $personRepository, private CanBeAddedOrUpdatedSpecification $canBeAddedOrUpdatedSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function handle(AddPersonCommand $command): void
    {
        $person = $this->buildPerson($command);

        $this->canBeAddedOrUpdatedSpecification->throwExceptionIfIsNotSatisfiedBy($person);

        $this->personRepository->add($person);

        $this->eventDispatcher->dispatchMany($person->releaseEvents());
    }

    private function buildPerson(AddPersonCommand $command): Person
    {
        return Person::create(
            new PersonId($command->id),
            new Name($command->lastName, $command->firstName, $command->middleName),
            new InsuranceCompanyId($command->insuranceCompanyId),
            $command->email ? new Email($command->email) : null,
            $command->phoneNumber ? new PhoneNumber($command->phoneNumber) : null,
            !is_null($command->notifierType) ? new NotifierType($command->notifierType) : null
        );
    }
}
