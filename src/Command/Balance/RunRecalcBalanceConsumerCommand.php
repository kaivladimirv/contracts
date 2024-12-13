<?php

declare(strict_types=1);

namespace App\Command\Balance;

use Override;
use App\Consumer\RecalcBalanceConsumer;
use App\Framework\Command\AbstractCommand;
use App\Framework\DIContainer\ContainerInterface;
use App\Service\Queue\ConsumerInterface;
use App\Service\Queue\QueueClientInterface;

/**
 * @psalm-api
 */
class RunRecalcBalanceConsumerCommand extends AbstractCommand
{
    public function __construct(private readonly ContainerInterface $container, private readonly QueueClientInterface $queueClient)
    {
    }

    #[Override]
    protected function fillExpectedArguments(): void
    {
    }

    #[Override]
    protected function execute(): void
    {
        /* @var ConsumerInterface $consumer */
        $consumer = $this->container->get(RecalcBalanceConsumer::class);

        $this->queueClient->connect();
        $this->queueClient->subscribe('recalc-balance', $consumer);

        $this->console->info('Ожидание новых запросов для обработки...');
        $this->queueClient->wait('recalc-balance');

        $this->queueClient->disconnect();

        $this->console->success('Обработка запросов завершена');
    }
}
