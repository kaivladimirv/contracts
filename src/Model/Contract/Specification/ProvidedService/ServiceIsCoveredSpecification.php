<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\ProvidedService;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;

class ServiceIsCoveredSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Услуга не покрывается договором';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly ContractServiceRepositoryInterface $contractServiceRepository)
    {
    }

    #[Override]
    public function isSatisfiedBy($entity): bool
    {
        /** @var ProvidedService $entity */

        try {
            $this->contractServiceRepository->getOne($entity->getContractId(), $entity->getService()->getId());

            return true;
        } catch (ContractServiceNotFoundException) {
            return false;
        }
    }
}
