<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\InsuredPerson\Add;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Entity\InsuredPerson\PersonId;
use App\Model\Contract\Repository\InsuredPerson\InsuredPersonRepositoryInterface;
use App\Model\Contract\Specification\InsuredPerson\CanBeAddedSpecification;

readonly class AddInsuredPersonHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private InsuredPersonRepositoryInterface $insuredPersonRepository, private CanBeAddedSpecification $canBeAddedSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    public function handle(AddInsuredPersonCommand $command): void
    {
        $insuredPerson = $this->buildInsuredPerson($command);

        $this->canBeAddedSpecification->throwExceptionIfIsNotSatisfiedBy($insuredPerson);

        $this->insuredPersonRepository->add($insuredPerson);

        $this->eventDispatcher->dispatchMany($insuredPerson->releaseEvents());
    }

    private function buildInsuredPerson(AddInsuredPersonCommand $command): InsuredPerson
    {
        return new InsuredPerson(
            new InsuredPersonId($command->id),
            new ContractId($command->contractId),
            new PersonId($command->personId),
            $command->policyNumber,
            $command->isAllowedToExceedLimit
        );
    }
}
