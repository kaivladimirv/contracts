<?php

declare(strict_types=1);

namespace App\Event\Dispatcher;

interface EventListenerInterface
{
    public function __invoke(): void;
}
