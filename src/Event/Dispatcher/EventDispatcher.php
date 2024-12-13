<?php

declare(strict_types=1);

namespace App\Event\Dispatcher;

use Override;
use App\Model\StoppableEventInterface;

readonly class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(private ListenerProviderInterface $listenerProvider)
    {
    }

    #[Override]
    public function dispatchMany(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    /**
     * @psalm-api
     */
    #[Override]
    public function dispatch(object $event): object
    {
        if (($event instanceof StoppableEventInterface) and $event->isPropagationStopped()) {
            return $event;
        }

        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            $listener($event);
        }

        return $event;
    }
}
