<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\ContractService;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Contract\Entity\ContractService\ContractService;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;

class IsUniqueSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Услуга уже добавлена в договор';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private readonly ContractServiceRepositoryInterface $contractServiceRepository)
    {
    }

    #[Override]
    public function isSatisfiedBy($entity): bool
    {
        /** @var ContractService $entity */

        try {
            $this->contractServiceRepository->getOne($entity->getContractId(), $entity->getServiceId());

            return false;
        } catch (ContractServiceNotFoundException) {
            return true;
        }
    }
}
