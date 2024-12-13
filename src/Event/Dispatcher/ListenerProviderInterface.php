<?php

declare(strict_types=1);

namespace App\Event\Dispatcher;

interface ListenerProviderInterface
{
    public function addListener(string $eventName, callable|EventListenerInterface $listener): void;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function addSubscriber(EventSubscriberInterface $subscriber): void;

    public function getListenersForEvent(object $event): iterable;
}
