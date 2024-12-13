<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use App\ReadModel\ProvidedService\ProvidedServiceFetcherInterface;

readonly class BalanceCalculator
{
    /**
     * @psalm-api
     */
    public function __construct(private ContractServiceRepositoryInterface $contractServiceRepository, private ProvidedServiceFetcherInterface $providedServiceFetcher)
    {
    }

    /**
     * @throws ContractServiceNotFoundException
     */
    public function calcByServiceAndInsured(ContractId $contractId, ServiceId $serviceId, InsuredPersonId $insuredPersonId): float
    {
        $contractService = $this->contractServiceRepository->getOne($contractId, $serviceId);
        $expense = $this->providedServiceFetcher->getExpenseByService($insuredPersonId, $serviceId, $contractService->getLimit()->getType());

        $expenseValue = ($contractService->getLimit()->getType()->isItAmountLimiter() ? $expense->getAmount() : $expense->getQuantity());

        return $contractService->getLimit()->getValue() - $expenseValue;
    }
}
