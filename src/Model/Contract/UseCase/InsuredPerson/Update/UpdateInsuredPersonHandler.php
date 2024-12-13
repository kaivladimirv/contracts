<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\InsuredPerson\Update;

use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;
use App\Model\Contract\Repository\InsuredPerson\InsuredPersonRepositoryInterface;
use App\Model\Contract\Specification\InsuredPerson\PolicyNumberIsUniqueSpecification;
use App\Event\Dispatcher\EventDispatcherInterface;

readonly class UpdateInsuredPersonHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private InsuredPersonRepositoryInterface $insuredPersonRepository, private PolicyNumberIsUniqueSpecification $policyNumberIsUniqueSpecification, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws InsuredPersonNotFoundException
     */
    public function handle(UpdateInsuredPersonCommand $command): void
    {
        $insuredPerson = $this->insuredPersonRepository->getOne(
            new InsuredPersonId($command->insuredPersonId)
        );

        $insuredPerson->changePolicyNumber($command->policyNumber);

        if ($command->isAllowedToExceedLimit) {
            $insuredPerson->allowToExceedLimit();
        } else {
            $insuredPerson->disallowToExceedLimit();
        }

        $this->policyNumberIsUniqueSpecification->throwExceptionIfIsNotSatisfiedBy($insuredPerson);

        $this->insuredPersonRepository->update($insuredPerson);

        $this->eventDispatcher->dispatchMany($insuredPerson->releaseEvents());
    }
}
