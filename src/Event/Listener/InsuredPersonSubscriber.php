<?php

declare(strict_types=1);

namespace App\Event\Listener;

use Override;
use App\Framework\DIContainer\ContainerInterface;
use App\Model\Contract\Event\InsuredPerson\InsuredPersonAddedEvent;
use App\Model\Contract\Event\InsuredPerson\PolicyNumberChangedEvent;
use App\Model\Contract\Service\InsuredPerson\CacheItemKeyGenerator;
use App\Model\Contract\UseCase\Balance\Recalc\ByInsured\RecalcBalanceByInsuredCommand;
use App\Model\Contract\UseCase\Balance\Recalc\ByInsured\RecalcBalanceByInsuredHandler;
use App\Service\Cache\CacheItemPoolInterface;
use App\Event\Dispatcher\EventSubscriberInterface;

readonly class InsuredPersonSubscriber implements EventSubscriberInterface
{
    public function __construct(private CacheItemPoolInterface $cache, private CacheItemKeyGenerator $cacheItemKeyGenerator, private ContainerInterface $container)
    {
    }

    #[Override]
    public function getSubscribedEvents(): array
    {
        return [
            InsuredPersonAddedEvent::class => 'onAdded',
            PolicyNumberChangedEvent::class => 'onPolicyNumberChanged',
        ];
    }

    public function onAdded(InsuredPersonAddedEvent $event): void
    {
        $command = new RecalcBalanceByInsuredCommand();
        $command->contractId = $event->getContractId()->getValue();
        $command->insuredPersonId = $event->getInsuredPersonId()->getValue();

        $handler = $this->container->get(RecalcBalanceByInsuredHandler::class);
        $handler->handle($command);
    }

    public function onPolicyNumberChanged(PolicyNumberChangedEvent $event): void
    {
        $this->cache->deleteItem($this->cacheItemKeyGenerator->generateFromPolicyNumber($event->getContractId(), $event->getOldPolicyNumber()));
    }
}
