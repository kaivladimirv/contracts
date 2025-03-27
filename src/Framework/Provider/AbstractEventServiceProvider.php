<?php

declare(strict_types=1);

namespace App\Framework\Provider;

use Override;
use App\Framework\DIContainer\ContainerInterface;
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
            $listener = $this->container->get($listenerClassName);

            $this->listenerProvider->addListener($eventName, $listener);
        }

        foreach ($this->subscribe as $subscriberClassName) {
            $subscriber = $this->container->get($subscriberClassName);

            $this->listenerProvider->addSubscriber($subscriber);
        }
    }
}
