<?php

declare(strict_types=1);

namespace App\Model\Person\UseCase\Update;

use App\Model\Person\Entity\Email;
use App\Model\Person\Entity\Name;
use App\Model\Person\Entity\NotifierType;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Entity\PhoneNumber;
use App\Model\Person\Exception\PersonNotFoundException;
use App\Model\Person\Repository\PersonRepositoryInterface;
use App\Model\Person\Specification\CanBeAddedOrUpdatedSpecification;
use App\Event\Dispatcher\EventDispatcherInterface;

readonly class UpdatePersonHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private PersonRepositoryInterface $personRepository, private CanBeAddedOrUpdatedSpecification $canBeAddedOrUpdatedSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws PersonNotFoundException
     */
    public function handle(UpdatePersonCommand $command): void
    {
        $person = $this->personRepository->getOne(new PersonId($command->id));

        $person->changeName(new Name($command->lastName, $command->firstName, $command->middleName));

        if ($command->email) {
            $person->changeEmail(new Email($command->email));
        } else {
            $person->deleteEmail();
        }

        if ($command->phoneNumber) {
            $person->changePhoneNumber(new PhoneNumber($command->phoneNumber));
        } else {
            $person->deletePhoneNumber();
        }

        if (!is_null($command->notifierType)) {
            $person->changeNotifierType(new NotifierType($command->notifierType));
        } else {
            $person->deleteNotifierType();
        }

        $this->canBeAddedOrUpdatedSpecification->throwExceptionIfIsNotSatisfiedBy($person);

        $this->personRepository->update($person);

        $this->eventDispatcher->dispatchMany($person->releaseEvents());
    }
}
