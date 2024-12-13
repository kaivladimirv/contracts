<?php

declare(strict_types=1);

namespace App\Model\Contract\Service\Balance;

use App\Framework\Console\ConsoleInterface;
use App\Model\Contract\Entity\InsuredPerson\InsuredPersonId;
use App\Service\Queue\QueueClientInterface;

readonly class BalanceNotifier
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private QueueClientInterface $queueClient, private ConsoleInterface $console)
    {
    }

    public function notify(InsuredPersonId $insuredPersonId): void
    {
        $this->queueClient->connect();
        $this->queueClient->publish(
            'balance-notifier',
            [
                'insuredPersonId' => $insuredPersonId->getValue(),
            ]
        );

        $this->console->info('Send event to balance-notifier');
    }
}
