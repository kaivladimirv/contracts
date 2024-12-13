<?php

declare(strict_types=1);

namespace App\Model\Person\UseCase\Delete;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Person\Entity\PersonId;
use App\Model\Person\Exception\PersonNotFoundException;
use App\Model\Person\Repository\PersonRepositoryInterface;

readonly class DeletePersonHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private PersonRepositoryInterface $personRepository, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws PersonNotFoundException
     */
    public function handle(DeletePersonCommand $command): void
    {
        $person = $this->personRepository->getOne(new PersonId($command->id));

        $person->delete();

        $this->personRepository->delete($person);

        $this->eventDispatcher->dispatchMany($person->releaseEvents());
    }
}
