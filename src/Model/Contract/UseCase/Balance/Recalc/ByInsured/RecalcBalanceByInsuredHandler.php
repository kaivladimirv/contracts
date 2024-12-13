<?php

declare(strict_types=1);

namespace App\Model\Contract\UseCase\Balance\Recalc\ByInsured;

use App\Model\Contract\Entity\Balance\Balance;
use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ContractService;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Model\Contract\Repository\Balance\BalanceRepositoryInterface;
use App\Model\Contract\Repository\ContractService\ContractServiceRepositoryInterface;

readonly class RecalcBalanceByInsuredHandler
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private BalanceRepositoryInterface $balanceRepository, private ContractServiceRepositoryInterface $contractServiceRepository)
    {
    }

    public function handle(RecalcBalanceByInsuredCommand $command): void
    {
        $insuredPersonId = new InsuredPersonId($command->insuredPersonId);
        $contractId = new ContractId($command->contractId);

        $contractServices = $this->contractServiceRepository->getAll($contractId);

        foreach ($contractServices as $contractService) {
            $balance = $this->buildBalance($insuredPersonId, $contractService);

            $this->balanceRepository->save($balance);
        }
    }

    private function buildBalance(InsuredPersonId $insuredPersonId, ContractService $contractService): Balance
    {
        return new Balance(
            $contractService->getContractId(),
            $insuredPersonId,
            $contractService->getServiceId(),
            $contractService->getLimit()->getType(),
            $contractService->getLimit()->getValue()
        );
    }
}
