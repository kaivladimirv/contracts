<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Balance\Recalc\ByServiceAndInsured;

use App\Model\Contract\Entity\Balance\Balance;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Exception\ContractService\ContractServiceNotFoundException;
use App\Model\Contract\Repository\Balance\BalanceRepositoryInterface;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;
use App\Model\Contract\Service\Balance\BalanceNotifier;
use App\Service\BalanceCalculator;

readonly class RecalcBalanceByServiceAndInsuredHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private BalanceCalculator $balanceCalculator, private ContractServiceRepositoryInterface $contractServiceRepository, private BalanceRepositoryInterface $balanceRepository, private BalanceNotifier $balanceNotifier)
    {
    }

    /**
     * @throws ContractServiceNotFoundException
     */
    public function handle(RecalcBalanceByServiceAndInsuredCommand $command): void
    {
        $contractId = new ContractId($command->contractId);
        $serviceId = new ServiceId($command->serviceId);
        $insuredPersonId = new InsuredPersonId($command->insuredPersonId);

        $calculatedBalanceValue = $this->balanceCalculator->calcByServiceAndInsured($contractId, $serviceId, $insuredPersonId);

        $balance = $this->buildBalance($contractId, $serviceId, $insuredPersonId, $calculatedBalanceValue);
        $this->balanceRepository->save($balance);

        $this->balanceNotifier->notify($insuredPersonId);
    }

    /**
     * @throws ContractServiceNotFoundException
     */
    private function buildBalance(
        ContractId $contractId,
        ServiceId $serviceId,
        InsuredPersonId $insuredPersonId,
        float $balanceValue
    ): Balance {
        $contractService = $this->contractServiceRepository->getOne($contractId, $serviceId);

        return new Balance(
            $contractId,
            $insuredPersonId,
            $serviceId,
            $contractService->getLimit()->getType(),
            $balanceValue
        );
    }
}
