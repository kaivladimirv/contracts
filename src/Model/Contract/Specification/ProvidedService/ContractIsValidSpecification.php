<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\ProvidedService;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;
use DateTimeImmutable;

class ContractIsValidSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Срок действия договора истёк';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly ContractRepositoryInterface $contractRepository)
    {
    }

    /**
     * @throws ContractNotFoundException
     */
    #[Override]
    public function isSatisfiedBy($entity): bool
    {
        /** @var ProvidedService $entity */

        $contract = $this->contractRepository->getOne($entity->getContractId());

        return !$contract->isExpiredTo(new DateTimeImmutable());
    }
}
