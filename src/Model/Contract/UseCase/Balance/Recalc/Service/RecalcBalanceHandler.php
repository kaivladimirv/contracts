<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Balance\Recalc\Service;

use App\Model\Contract\Entity\Balance\Balance;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ContractService;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPerson;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Repository\Balance\BalanceRepositoryInterface;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use App\Model\Contract\Service\Balance\BalanceCalculator;
use App\Model\Contract\Service\Balance\BalanceNotifier;
use App\ReadModel\Contract\InsuredPerson\InsuredPersonFetcherInterface;
use App\ReadModel\ProvidedService\ProvidedServiceFetcherInterface;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;

readonly class RecalcBalanceHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private BalanceRepositoryInterface $balanceRepository, private InsuredPersonFetcherInterface $insuredPersonFetcher, private ContractServiceRepositoryInterface $contractServiceRepository, private ProvidedServiceFetcherInterface $providedServiceFetcher, private BalanceCalculator $balanceCalculator, private BalanceNotifier $balanceNotifier)
    {
    }

    /**
     * @throws ContractServiceNotFoundException
     */
    public function handle(RecalcBalanceCommand $command): void
    {
        $contractId = new ContractId($command->contractId);
        $serviceId = new ServiceId($command->serviceId);
        $contractService = $this->contractServiceRepository->getOne($contractId, $serviceId);

        $insuredPersonIds = $this->insuredPersonFetcher->getAllIds($contractId);

        /* @var InsuredPerson $insuredPerson */
        foreach ($insuredPersonIds as $insuredPersonId) {
            $insuredPersonId = new InsuredPersonId($insuredPersonId);
            $balance = $this->buildBalance($insuredPersonId, $contractService);

            $this->balanceRepository->save($balance);

            $this->balanceNotifier->notify($insuredPersonId);
        }
    }

    private function buildBalance(InsuredPersonId $insuredPersonId, ContractService $contractService): Balance
    {
        $balance = $this->calcBalance($insuredPersonId, $contractService);

        return new Balance(
            $contractService->getContractId(),
            $insuredPersonId,
            $contractService->getServiceId(),
            $contractService->getLimit()->getType(),
            $balance
        );
    }

    private function calcBalance(InsuredPersonId $insuredPersonId, ContractService $contractService): float
    {
        $expense = $this->providedServiceFetcher->getExpenseByService(
            $insuredPersonId,
            $contractService->getServiceId(),
            $contractService->getLimit()->getType()
        );

        return $this->balanceCalculator->calc($contractService->getLimit(), $expense);
    }
}
