<?php

declare(strict_types=1);

namespace App\Model;

use Override;
use DateTimeImmutable;

abstract class AbstractDomainEvent implements DomainEventInterface, StoppableEventInterface
{
    private bool $isPropagationStopped = false;
    protected DateTimeImmutable $dateOccurred;

    #[Override]
    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }

    #[Override]
    public function stopPropagation(): void
    {
        $this->isPropagationStopped = true;
    }

    #[Override]
    public function getDateOccurred(): DateTimeImmutable
    {
        return $this->dateOccurred;
    }
}
