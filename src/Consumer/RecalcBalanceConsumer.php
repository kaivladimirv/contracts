<?php

declare(strict_types=1);

namespace App\Consumer;

use Override;
use App\Framework\Console\ConsoleInterface;
use App\Model\Contract\UseCase\Balance\Recalc\Service\RecalcBalanceCommand;
use App\Model\Contract\UseCase\Balance\Recalc\Service\RecalcBalanceHandler;
use App\Service\Hydrator\HydratorInterface;
use App\Service\Queue\ConsumerInterface;
use App\Service\Queue\QueueMessage;
use Exception;

readonly class RecalcBalanceConsumer implements ConsumerInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ConsoleInterface $console, private RecalcBalanceHandler $handler, private HydratorInterface $hydrator)
    {
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function consume(QueueMessage $message): void
    {
        $data = json_decode((string) $message->getBody(), true);

        $command = $this->hydrator->hydrate(RecalcBalanceCommand::class, $data);

        $this->handler->handle($command);

        $this->console->success('Баланс по лимиту пересчитан для услуги ' . $command->serviceId);
    }
}
