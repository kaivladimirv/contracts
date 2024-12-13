<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\ProvidedService;

use Override;
use App\Model\AbstractSpecification;
use App\Model\Contract\Entity\ProvidedService\ProvidedService;
use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;

class DateOfServiceIsIncludedInContractPeriodSpecification extends AbstractSpecification
{
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

        if ($contract->getPeriod()->isDateIncluded($entity->getDateOfService())) {
            return true;
        }

        $this->reasonNotSatisfied =
            'Дата оказания услуги не входит в период действия договора '
            . $contract->getPeriod()->getStartDate()->format('d.m.Y') . ' - '
            . $contract->getPeriod()->getEndDate()->format('d.m.Y');

        return false;
    }
}
