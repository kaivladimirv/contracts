<?php

declare(strict_types=1);

namespace App\Event\Listener;

use Override;
use App\Event\Dispatcher\EventSubscriberInterface;
use App\Model\Contract\Event\ProvidedService\ProvidedServiceCanceledEvent;
use App\Model\Contract\Event\ProvidedService\ProvidedServiceRegisteredEvent;
use App\Service\Queue\QueueClientInterface;

readonly class ProvidedServiceSubscriber implements EventSubscriberInterface
{
    public function __construct(private QueueClientInterface $queueClient)
    {
    }

    #[Override]
    public function getSubscribedEvents(): array
    {
        return [
            ProvidedServiceRegisteredEvent::class => 'onHappened',
            ProvidedServiceCanceledEvent::class => 'onHappened',
        ];
    }

    public function onHappened(ProvidedServiceRegisteredEvent|ProvidedServiceCanceledEvent $event): void
    {
        $this->queueClient->connect();
        $this->queueClient->publish(
            'recalc-balance-by-service-and-insured',
            [
                'contractId' => $event->getContractId()->getValue(),
                'insuredPersonId' => $event->getInsuredPersonId()->getValue(),
                'serviceId' => $event->getService()->getId()->getValue(),
            ]
        );
    }
}
