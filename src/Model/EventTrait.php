<?php

declare(strict_types=1);

namespace App\Model;

trait EventTrait
{
    private array $events = [];

    public function registerEvent(object $event): void
    {
        $this->events[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
