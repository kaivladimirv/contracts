<?php

declare(strict_types=1);

namespace App\Command\Balance;

use Override;
use App\Consumer\RecalcBalanceByServiceAndInsuredConsumer;
use App\Framework\Command\AbstractCommand;
use App\Service\Queue\ConsumerRunner;

/**
 * @psalm-api
 */
class RunRecalcBalanceByServiceAndInsuredConsumerCommand extends AbstractCommand
{
    public function __construct(private readonly ConsumerRunner $consumerRunner)
    {
    }

    #[Override]
    protected function fillExpectedArguments(): void
    {
    }

    #[Override]
    protected function execute(): void
    {
        $this->consumerRunner->run(
            'recalc-balance-by-service-and-insured',
            RecalcBalanceByServiceAndInsuredConsumer::class
        );
    }
}
