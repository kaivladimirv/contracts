<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\InsuredPerson\Delete;

use App\Event\Dispatcher\EventDispatcherInterface;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonInUseException;
use App\Model\Contract\Exception\InsuredPerson\InsuredPersonNotFoundException;
use App\Model\Contract\Repository\InsuredPerson\InsuredPersonRepositoryInterface;
use App\ReadModel\ProvidedService\ProvidedServiceFetcherInterface;

readonly class DeleteInsuredPersonHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private InsuredPersonRepositoryInterface $insuredPersonRepository, private ProvidedServiceFetcherInterface $providedServiceFetcher, private EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * @throws InsuredPersonInUseException
     * @throws InsuredPersonNotFoundException
     */
    public function handle(DeleteInsuredPersonCommand $command): void
    {
        $insuredPerson = $this->insuredPersonRepository->getOne(
            new InsuredPersonId($command->insuredPersonId)
        );

        $this->throwExceptionIfInsuredPersonInUse($insuredPerson->getId());

        $insuredPerson->delete();

        $this->insuredPersonRepository->delete($insuredPerson);

        $this->eventDispatcher->dispatchMany($insuredPerson->releaseEvents());
    }

    /**
     * @throws InsuredPersonInUseException
     */
    private function throwExceptionIfInsuredPersonInUse(InsuredPersonId $id): void
    {
        if ($this->providedServiceFetcher->existsForInsuredPerson($id)) {
            throw new InsuredPersonInUseException('Застрахованному лицу уже были оказаны услуги');
        }
    }
}
