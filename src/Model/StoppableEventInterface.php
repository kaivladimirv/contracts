<?php

declare(strict_types=1);

namespace App\Model;

interface StoppableEventInterface
{
    public function isPropagationStopped(): bool;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function stopPropagation(): void;
}
