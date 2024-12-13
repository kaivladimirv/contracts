<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\Balance;

use App\Model\Contract\Entity\Contract\ContractId;
use App\Model\Contract\Entity\ContractService\ServiceId;
use App\Service\Queue\QueueClientInterface;

readonly class RecalcBalanceRunner
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private QueueClientInterface $queueClient)
    {
    }

    public function run(ContractId $contractId, ServiceId $serviceId): void
    {
        $this->queueClient->connect();
        $this->queueClient->publish(
            'recalc-balance',
            [
                'contractId' => $contractId->getValue(),
                'serviceId'  => $serviceId->getValue(),
            ]
        );
    }
}
