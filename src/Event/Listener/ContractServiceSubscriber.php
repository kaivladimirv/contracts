<?php

declare(strict_types=1);

namespace App\Event\Listener;

use Override;
use App\Model\Contract\Event\ContractService\ContractServiceAddedEvent;
use App\Event\Dispatcher\EventSubscriberInterface;
use App\Model\Contract\Event\ContractService\ContractServiceLimitChangedEvent;
use App\Model\Contract\Service\Balance\RecalcBalanceRunner;
use App\Model\DomainEventInterface;

readonly class ContractServiceSubscriber implements EventSubscriberInterface
{
    public function __construct(private RecalcBalanceRunner $recalcBalanceRunner)
    {
    }

    #[Override]
    public function getSubscribedEvents(): array
    {
        return [
            ContractServiceAddedEvent::class => 'onHappened',
            ContractServiceLimitChangedEvent::class => 'onHappened',
        ];
    }

    public function onHappened(DomainEventInterface $event): void
    {
        /** @var ContractServiceAddedEvent|ContractServiceLimitChangedEvent $event */
        $this->recalcBalanceRunner->run($event->getContractId(), $event->getServiceId());
    }
}
