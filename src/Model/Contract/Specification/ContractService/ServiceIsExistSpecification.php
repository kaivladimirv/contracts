<?php

declare(strict_types=1);

namespace App\Model\Contract\Specification\ContractService;

use App\Model\Contract\Exception\Contract\ContractNotFoundException;
use App\Model\Contract\Repository\Contract\ContractRepositoryInterface;
use App\Model\Service\Entity\InsuranceCompanyId;
use App\Model\Service\Entity\ServiceId;
use App\ReadModel\Service\ServiceFetcherInterface;
use Override;
use App\Model\AbstractSpecification;
use App\Model\Contract\Entity\ContractService\ContractService;

class ServiceIsExistSpecification extends AbstractSpecification
{
    protected string $reasonNotSatisfied = 'Услуга не найдена в справочнике услуг';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        private readonly ServiceFetcherInterface $serviceFetcher,
        private readonly ContractRepositoryInterface $contractRepository
    ) {
    }

    /**
     * @param ContractService|object $entity
     * @throws ContractNotFoundException
     */
    #[Override]
    public function isSatisfiedBy($entity): bool
    {
        $insuranceCompanyId = $this->contractRepository->getOne($entity->getContractId())->getInsuranceCompanyId();
        $insuranceCompanyId = new InsuranceCompanyId($insuranceCompanyId->getValue());
        $serviceId = new ServiceId($entity->getServiceId()->getValue());

        return $this->serviceFetcher->isExist($insuranceCompanyId, $serviceId);
    }
}
