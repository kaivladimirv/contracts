<?php

declare(strict_types=1);

namespace App\Event\Dispatcher;

interface EventDispatcherInterface
{
    public function dispatchMany(array $events): void;

    /**
     * @psalm-api
     */
    public function dispatch(object $event): object;
}
