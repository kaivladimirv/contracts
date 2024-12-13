<?php

declare(strict_types=1);

namespace App\Event\Dispatcher;

use Override;

class ListenerProvider implements ListenerProviderInterface
{
    private array $listeners = [];

    #[Override]
    public function addListener(string $eventName, callable|EventListenerInterface $listener): void
    {
        $this->listeners[$eventName][] = $listener;
    }

    #[Override]
    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $methodName) {
            $this->addListener(
                $eventName,
                fn($event) => call_user_func(
                    [
                        $subscriber,
                        $methodName,
                    ],
                    $event
                )
            );
        }
    }

    /**
     * @param object|string $event
     */
    #[Override]
    public function getListenersForEvent($event): iterable
    {
        $eventName = is_string($event) ? $event : $event::class;

        return $this->listeners[$eventName] ?? [];
    }
}
