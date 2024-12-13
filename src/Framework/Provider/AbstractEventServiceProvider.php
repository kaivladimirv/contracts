<?php

declare(strict_types=1);

namespace App\Framework\Provider;

use Override;
use App\Framework\DIContainer\ContainerInterface;
use App\Event\Dispatcher\EventListenerInterface;
use App\Event\Dispatcher\EventSubscriberInterface;
use App\Event\Dispatcher\ListenerProviderInterface;

abstract class AbstractEventServiceProvider implements ServiceProviderInterface
{
    protected array $listen    = [];
    protected array $subscribe = [];

    final public function __construct(private readonly ListenerProviderInterface $listenerProvider, private readonly ContainerInterface $container)
    {
    }

    #[Override]
    final public function register(): void
    {
        foreach ($this->listen as $eventName => $listenerClassName) {
            /** @var EventListenerInterface $listener */
            $listener = $this->container->get($listenerClassName);

            $this->listenerProvider->addListener($eventName, $listener);
        }

        foreach ($this->subscribe as $subscriberClassName) {
            /** @var EventSubscriberInterface $subscriber */
            $subscriber = $this->container->get($subscriberClassName);

            $this->listenerProvider->addSubscriber($subscriber);
        }
    }
}
