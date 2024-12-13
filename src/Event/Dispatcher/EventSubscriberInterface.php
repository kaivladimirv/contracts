<?php

declare(strict_types=1);

namespace App\Event\Dispatcher;

interface EventSubscriberInterface
{
    public function getSubscribedEvents(): array;
}
