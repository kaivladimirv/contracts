<?php

declare(strict_types=1);

namespace App\Service\Queue;

use App\Framework\Console\ConsoleInterface;
use App\Framework\DIContainer\ContainerInterface;

class ConsumerRunner
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(protected ConsoleInterface $console, private readonly ContainerInterface $container, private readonly QueueClientInterface $queueClient)
    {
    }

    /**
     * @param class-string<ConsumerInterface> $consumerClassName
     */
    public function run(string $queueName, string $consumerClassName): void
    {
        $consumer = $this->container->get($consumerClassName);

        $this->queueClient->connect();
        $this->queueClient->subscribe($queueName, $consumer);

        $this->console->info("$queueName. Ожидание новых запросов для обработки...");
        $this->queueClient->wait($queueName);

        $this->queueClient->disconnect();

        $this->console->success("$queueName. Обработка запросов завершена");
    }
}
