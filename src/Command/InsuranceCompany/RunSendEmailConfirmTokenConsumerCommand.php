<?php

declare(strict_types=1);

namespace App\Command\InsuranceCompany;

use Override;
use App\Consumer\SendEmailConfirmTokenConsumer;
use App\Framework\Command\AbstractCommand;
use App\Framework\DIContainer\ContainerInterface;
use App\Service\Queue\ConsumerInterface;
use App\Service\Queue\QueueClientInterface;

/**
 * @psalm-api
 */
class RunSendEmailConfirmTokenConsumerCommand extends AbstractCommand
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
        $consumer = $this->container->get(SendEmailConfirmTokenConsumer::class);

        $this->queueClient->connect();
        $this->queueClient->subscribe('send-email-confirm-token', $consumer);

        $this->console->info('Ожидание новых запросов для обработки...');
        $this->queueClient->wait('send-email-confirm-token');

        $this->queueClient->disconnect();

        $this->console->success('Обработка запросов завершена');
    }
}
